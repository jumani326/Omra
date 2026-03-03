<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Dompdf\Dompdf;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $service
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', \App\Models\Payment::class);
        $payments = $this->service->getAll($request->all());
        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', \App\Models\Payment::class);
        $pilgrimId = $request->get('pilgrim_id');
        $pilgrim = $pilgrimId ? \App\Models\Pilgrim::find($pilgrimId) : null;
        $u = auth()->user();
        $branchId = $u->hasRole('Super Admin Agence') ? session('current_branch_id') : $u->branch_id;
        $pilgrims = \App\Models\Pilgrim::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(!$branchId && ($agencyId = $u->agence_id ?? $u->branch?->agency_id), fn ($q) => $q->where('agence_id', $agencyId))
            ->orderBy('last_name')->get();
        $nextRefNo = $this->service->getNextRefNo();
        return view('payments.create', compact('pilgrims', 'pilgrim', 'nextRefNo'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->service->create($request->validated());
        return redirect()->route('payments.show', $payment)->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(int $id): View|RedirectResponse
    {
        $payment = $this->service->findById($id);
        if (!$payment) {
            abort(404);
        }
        Gate::authorize('view', $payment);
        return view('payments.show', compact('payment'));
    }

    public function edit(int $id): View
    {
        $payment = $this->service->findById($id);
        if (!$payment) {
            abort(404);
        }
        Gate::authorize('update', $payment);
        return view('payments.edit', compact('payment'));
    }

    public function update(UpdatePaymentRequest $request, int $id): RedirectResponse
    {
        $payment = $this->service->findById($id);
        if (!$payment) {
            abort(404);
        }
        $this->service->update($payment, $request->validated());
        return redirect()->route('payments.show', $payment)->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(int $id): RedirectResponse
    {
        abort(403, 'Les paiements ne peuvent pas être supprimés. Utilisez le statut « Remboursé » si besoin.');
    }

    public function invoice(\App\Models\Payment $payment): Response
    {
        Gate::authorize('view', $payment);
        $payment->load('pilgrim');
        $html = view('payments.invoice', compact('payment'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-' . $payment->ref_no . '.pdf"',
        ]);
    }
}
