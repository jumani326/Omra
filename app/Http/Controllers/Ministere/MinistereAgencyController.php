<?php

namespace App\Http\Controllers\Ministere;

use App\Http\Controllers\Controller;
use App\Mail\SerinityAgencyValidatedMail;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MinistereAgencyController extends Controller
{
    public function validateAgency(Agency $agency)
    {
        $agency->update([
            'validated' => true,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'ministry_status' => 'approved',
        ]);

        foreach ($agency->users as $user) {
            Mail::to($user->email)->send(new SerinityAgencyValidatedMail($user, route('login')));
        }

        return redirect()->back()->with('success', 'L\'agence a été validée. Un email a été envoyé aux responsables.');
    }

    public function suspendAgency(Agency $agency)
    {
        $agency->update([
            'validated' => false,
            'ministry_status' => 'revoked',
        ]);

        return redirect()->back()->with('success', 'L\'agence a été désactivée.');
    }
}
