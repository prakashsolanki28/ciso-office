<?php

namespace Modules\Manjulika\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Manjulika\Models\CyberChatSession;

class ChatSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = CyberChatSession::query()
            ->withCount('messages')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest('last_active_at')
            ->paginate(20)
            ->withQueryString();

        return view('manjulika::sessions.index', compact('sessions'));
    }

    public function show(CyberChatSession $session)
    {
        $session->load('messages');

        return view('manjulika::sessions.show', compact('session'));
    }

    public function destroy(CyberChatSession $session)
    {
        $session->delete();

        return redirect()
            ->route('manjulika.sessions.index')
            ->with('success', 'Chat session deleted.');
    }
}
