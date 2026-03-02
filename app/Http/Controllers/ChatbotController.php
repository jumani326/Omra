<?php

namespace App\Http\Controllers;

use App\Models\Pilgrim;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * Retourne la session de chat du pèlerin connecté (par email).
     */
    public function session()
    {
        $pilgrim = Pilgrim::where('email', Auth::user()->email)->first();
        if (!$pilgrim) {
            return response()->json(['has_pilgrim' => false, 'messages' => []]);
        }
        $chat = ChatSession::firstOrCreate(
            ['pilgrim_id' => $pilgrim->id],
            ['messages' => [], 'lang' => 'fr', 'escalated' => false]
        );
        return response()->json([
            'has_pilgrim' => true,
            'messages' => $chat->messages ?? [],
        ]);
    }

    /**
     * Envoie un message et retourne la réponse du bot (stub / futur ChatbotService).
     */
    public function sendMessage(Request $request)
    {
        $request->validate(['content' => 'required|string|max:2000']);
        $pilgrim = Pilgrim::where('email', Auth::user()->email)->first();
        if (!$pilgrim) {
            return response()->json(['error' => 'Aucun dossier pèlerin. Choisissez d\'abord un forfait.'], 403);
        }
        $chat = ChatSession::firstOrCreate(
            ['pilgrim_id' => $pilgrim->id],
            ['messages' => [], 'lang' => 'fr', 'escalated' => false]
        );
        $messages = $chat->messages ?? [];
        $messages[] = ['role' => 'user', 'content' => $request->content, 'at' => now()->toIso8601String()];
        $reply = $this->getBotReply($request->content, $pilgrim);
        $messages[] = ['role' => 'assistant', 'content' => $reply, 'at' => now()->toIso8601String()];
        $chat->update(['messages' => $messages]);
        return response()->json(['message' => $reply]);
    }

    private function getBotReply(string $userMessage, Pilgrim $pilgrim): string
    {
        $msg = mb_strtolower($userMessage);
        if (str_contains($msg, 'visa')) {
            $status = $pilgrim->visa?->status ?? 'non soumis';
            return "Pour votre dossier, le statut du visa est actuellement : " . $status . ". Pour plus de détails, contactez votre agence.";
        }
        if (str_contains($msg, 'paiement') || str_contains($msg, 'payer') || str_contains($msg, 'solde')) {
            $total = $pilgrim->payments()->where('status', 'completed')->sum('amount');
            return "Vos paiements enregistrés s'élèvent à " . number_format($total, 0) . " FDJ. Pour le solde ou les échéances, contactez le comptable de votre agence.";
        }
        if (str_contains($msg, 'document') || str_contains($msg, 'dossier')) {
            return "Pour compléter votre dossier, fournissez : passeport, photos, certificat médical. Déposez-les auprès de votre agence ou via l'espace prévu.";
        }
        if (str_contains($msg, 'bonjour') || str_contains($msg, 'salut') || str_contains($msg, 'hello')) {
            return "Bonjour ! Je suis l'assistant Omra. Vous pouvez me demander où en est votre visa, votre paiement ou les documents à fournir.";
        }
        return "Merci pour votre message. Pour une question personnalisée, contactez votre agence. Vous pouvez aussi demander : statut visa, paiement, ou documents à fournir.";
    }
}
