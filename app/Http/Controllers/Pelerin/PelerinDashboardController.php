<?php

namespace App\Http\Controllers\Pelerin;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use App\Models\PilgrimDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PelerinDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)
            ->with(['package', 'package.hotelMecca', 'package.hotelMedina', 'visa', 'payments', 'group', 'guide.user', 'documents', 'transactionsDigitales.compteMarchand'])
            ->first();

        $procedureSteps = [
            ['key' => 'registered', 'label' => 'Inscrit', 'done' => $pilgrim && in_array($pilgrim->status, ['registered', 'dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned'])],
            ['key' => 'payment', 'label' => 'Paiement', 'done' => $pilgrim && $pilgrim->payments()->where('status', 'completed')->exists()],
            ['key' => 'processing', 'label' => 'Traitement', 'done' => $pilgrim && in_array($pilgrim->status, ['dossier_complete', 'visa_submitted', 'visa_approved', 'departed', 'returned'])],
            ['key' => 'issued', 'label' => 'Émis', 'done' => $pilgrim && in_array($pilgrim->status, ['visa_approved', 'departed', 'returned'])],
        ];

        return view('pelerin.dashboard', compact('pilgrim', 'procedureSteps'));
    }

    public function profile()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)
            ->with(['package', 'visa', 'payments', 'group', 'guide.user', 'documents'])
            ->first();

        return view('pelerin.profile', compact('pilgrim'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $pilgrim = Pilgrim::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('pilgrims', 'email')->ignore($pilgrim->id),
            ],
            'phone' => ['required', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'passport_no' => ['nullable', 'string', 'max:255'],
            'documents.passport' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:4096'],
            'documents.medical_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Mettre à jour les champs de base du pèlerin
        $pilgrim->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'nationality' => $validated['nationality'],
            'passport_no' => $validated['passport_no'] ?? $pilgrim->passport_no,
        ]);

        $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
        $user->email = $validated['email'];
        $user->save();

        // Upload de nouveaux documents si présents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("pilgrims/{$pilgrim->id}/documents", 'public');

                    $pilgrim->documents()->create([
                        'type' => $type,
                        'file_path' => $path,
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        return redirect()
            ->route('pelerin.profile')
            ->with('status', 'Profil mis à jour avec succès.');
    }

    /**
     * Espace documents : permet au pèlerin de déposer ses pièces (passeport, photo, certificat)
     * directement depuis le tableau de bord client.
     */
    public function uploadDocuments(Request $request)
    {
        $user = Auth::user();

        $pilgrim = Pilgrim::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->firstOrFail();

        $validated = $request->validate([
            'documents.passport' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:4096'],
            'documents.medical_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("pilgrims/{$pilgrim->id}/documents", 'public');

                    $pilgrim->documents()->create([
                        'type' => $type,
                        'file_path' => $path,
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        return redirect()
            ->route('pelerin.dashboard')
            ->with('success', 'Vos documents ont été envoyés à l\'agence. Elle les vérifiera avant de déposer votre demande de visa.');
    }
}
