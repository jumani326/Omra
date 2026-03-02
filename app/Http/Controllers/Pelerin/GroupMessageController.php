<?php

namespace App\Http\Controllers\Pelerin;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use App\Models\Pilgrim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        if (! $pilgrim || ! $pilgrim->group_id) {
            return view('group-messages.index', [
                'group' => null,
                'messages' => collect(),
                'pageTitle' => 'Messagerie avec le guide',
            ]);
        }

        $group = $pilgrim->group;
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
        $pilgrim = Pilgrim::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        if (! $pilgrim || ! $pilgrim->group_id) {
            return redirect()->route('pelerin.messages.index')->with('error', 'Vous n\'êtes pas encore assigné à un groupe.');
        }

        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        GroupMessage::create([
            'group_id' => $pilgrim->group_id,
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        return redirect()->route('pelerin.messages.index')->with('success', 'Message envoyé.');
    }
}
