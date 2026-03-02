<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guide = $user->guide;
        if (! $guide || ! $guide->group_id) {
            return view('group-messages.index', [
                'group' => null,
                'messages' => collect(),
                'pageTitle' => 'Messagerie groupe',
            ]);
        }

        $group = $guide->group;
        $messages = GroupMessage::where('group_id', $group->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('group-messages.index', [
            'group' => $group,
            'messages' => $messages,
            'pageTitle' => 'Messagerie - ' . $group->name,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $guide = $user->guide;
        if (! $guide || ! $guide->group_id) {
            return redirect()->route('guide.messages.index')->with('error', 'Aucun groupe assigné.');
        }

        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        GroupMessage::create([
            'group_id' => $guide->group_id,
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        return redirect()->route('guide.messages.index')->with('success', 'Message envoyé.');
    }
}
