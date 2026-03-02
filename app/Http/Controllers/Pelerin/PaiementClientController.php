<?php

namespace App\Http\Controllers\Pelerin;

use App\Http\Controllers\Controller;
use App\Mail\TransactionDigitaleFicheMail;
use App\Models\CompteMarchand;
use App\Models\TransactionDigitale;
use App\Models\Pilgrim;
use Dompdf\Dompdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaiementClientController extends Controller
{
    protected function getPilgrim(): Pilgrim
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->firstOrFail();
        return $pilgrim;
    }

    /**
     * Retourne le compte marchand actif pour la branche du pèlerin et la méthode demandée.
     */
    public function compteMarchandByMethod(Request $request): JsonResponse
    {
        $request->validate(['methode' => 'required|string|in:D-money,Waafi,MyCac']);
        $pilgrim = $this->getPilgrim();

        $compte = CompteMarchand::pourBranch($pilgrim->branch_id)
            ->actifs()
            ->where('nom_methode', $request->methode)
            ->first();

        if (!$compte) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte marchand disponible pour cette méthode.',
            ], 404);
        }

        $logoMap = [
            'D-money' => 'D-money.jpeg',
            'Waafi'   => 'Waafi.jpeg',
            'MyCac'   => 'Mycac.jpeg',
        ];
        $logo = $logoMap[$compte->nom_methode] ?? null;

        return response()->json([
            'success' => true,
            'compte' => [
                'id' => $compte->id,
                'nom_agence' => $compte->nom_agence,
                'numero_compte' => $compte->numero_compte,
                'nom_methode' => $compte->nom_methode,
                'logo' => $logo ? asset('img/' . $logo) : null,
            ],
        ]);
    }

    /**
     * Enregistre une transaction digitale (client), génère la fiche PDF, envoie à l'agence.
     */
    public function store(Request $request): JsonResponse
    {
        $pilgrim = $this->getPilgrim();

        $validated = $request->validate([
            'compte_marchand_id' => ['required', 'integer', 'exists:compte_marchands,id'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $compte = CompteMarchand::findOrFail($validated['compte_marchand_id']);
        // Vérifier que le compte appartient à la branche du pèlerin
        if ($compte->branch_id !== null && $compte->branch_id !== $pilgrim->branch_id) {
            return response()->json(['success' => false, 'message' => 'Compte non autorisé.'], 403);
        }
        if (!$compte->actif) {
            return response()->json(['success' => false, 'message' => 'Ce compte n\'est plus actif.'], 403);
        }

        $clientNom = $pilgrim->first_name . ' ' . $pilgrim->last_name;

        $transaction = TransactionDigitale::create([
            'compte_marchand_id' => $compte->id,
            'pilgrim_id' => $pilgrim->id,
            'montant' => $validated['montant'],
            'client_nom' => $clientNom,
            'statut' => 'en_attente',
            'reference' => $validated['reference'] ?? null,
            'notes' => null,
        ]);

        $pdfPath = $this->generateAndStoreFichePdf($transaction);
        if ($pdfPath) {
            $transaction->update(['pdf_path' => $pdfPath]);
        }

        $this->sendFicheToAgency($transaction);

        return response()->json([
            'success' => true,
            'message' => 'Paiement envoyé avec succès. En attente de validation par l\'agence.',
            'transaction_id' => $transaction->id,
            'fiche_url' => $transaction->pdf_path ? route('pelerin.fiche-paiement.pdf', $transaction) : null,
        ]);
    }

    protected function generateAndStoreFichePdf(TransactionDigitale $transaction): ?string
    {
        $transaction->load(['compteMarchand', 'pilgrim']);
        $html = view('pdf.transaction-digitale-fiche', ['transaction' => $transaction])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dir = 'fiches-paiement/' . $transaction->id;
        $filename = 'fiche-paiement-' . $transaction->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        $path = $dir . '/' . $filename;
        Storage::disk('public')->put($path, $dompdf->output());
        return $path;
    }

    protected function sendFicheToAgency(TransactionDigitale $transaction): void
    {
        $compte = $transaction->compteMarchand;
        $branch = $compte->branch;
        $agency = $branch?->agency ?? null;
        $emails = [];
        if ($agency) {
            $emails = $agency->users()->pluck('email')->filter()->toArray();
        }
        if (empty($emails) && $branch) {
            $emails = $branch->users()->pluck('email')->filter()->toArray();
        }
        if (empty($emails)) {
            return;
        }
        foreach (array_unique($emails) as $email) {
            try {
                Mail::to($email)->send(new TransactionDigitaleFicheMail($transaction));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Téléchargement de la fiche PDF par le client (espace pèlerin).
     */
    public function fichePdf(TransactionDigitale $transaction): Response
    {
        $pilgrim = $this->getPilgrim();
        if ($transaction->pilgrim_id !== $pilgrim->id) {
            abort(403);
        }

        if ($transaction->pdf_path && Storage::disk('public')->exists($transaction->pdf_path)) {
            $content = Storage::disk('public')->get($transaction->pdf_path);
            $filename = 'fiche-paiement-' . $transaction->id . '.pdf';
            return new Response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        // Régénérer le PDF si absent
        $transaction->load(['compteMarchand', 'pilgrim']);
        $html = view('pdf.transaction-digitale-fiche', ['transaction' => $transaction])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'fiche-paiement-' . $transaction->id . '.pdf';
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
