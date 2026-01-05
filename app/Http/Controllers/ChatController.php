<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    /* ===============================
     * SESSION IDENTIFIER
     * =============================== */
    private function ownerKey(Request $request): string
    {
        if (!$request->session()->has('owner_key')) {
            $request->session()->put('owner_key', sha1(Str::uuid()));
        }
        return $request->session()->get('owner_key');
    }

    /* ===============================
     * ENSURE CONVERSATION
     * =============================== */
    private function ensureConversation(Request $request): Conversation
    {
        $ownerKey = $this->ownerKey($request);
        $convId   = $request->session()->get('conv_id');

        if ($convId) {
            $conv = Conversation::where('id', $convId)
                ->where('owner_key', $ownerKey)
                ->first();
            if ($conv) return $conv;
        }

        $conv = Conversation::create([
            'id'         => (string) Str::uuid(),
            'owner_key'  => $ownerKey,
            'title'      => 'Percakapan Baru',
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $request->session()->put('conv_id', $conv->id);
        return $conv;
    }

    /* ===============================
     * HALAMAN CHAT
     * =============================== */
    public function index(Request $request)
    {
        $ownerKey = $this->ownerKey($request);
        $current  = $this->ensureConversation($request);

        return view('chat', [
            'conversations' => Conversation::where('owner_key', $ownerKey)
                ->orderByDesc('updated_at')
                ->get(),
            'currentConversation' => $current,
            'messages' => Message::where('conversation_id', $current->id)
                ->orderBy('id')
                ->get(),
        ]);
    }

    /* ===============================
     * BUAT PERCAKAPAN BARU
     * =============================== */
    public function newConversation(Request $request)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::create([
            'id'         => (string) Str::uuid(),
            'owner_key'  => $ownerKey,
            'title'      => 'Percakapan Baru',
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $request->session()->put('conv_id', $conv->id);

        return response()->json([
            'ok' => true,
            'conversation_id' => $conv->id,
        ]);
    }

    /* ===============================
     * BUKA PERCAKAPAN
     * =============================== */
    public function openConversation(Request $request, string $id)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::where('id', $id)
            ->where('owner_key', $ownerKey)
            ->firstOrFail();

        $request->session()->put('conv_id', $conv->id);

        return redirect()->route('chat.index');
    }

    /* ===============================
     * CHAT KE AI
     * =============================== */
    public function chat(Request $request, ChatbotService $chatbot)
    {
        $rateKey = 'chat:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            return response()->json([
                'ok' => false,
                'reply' => 'Terlalu banyak permintaan. Tunggu sebentar.',
            ], 429);
        }
        RateLimiter::hit($rateKey, 60);

        $data = $request->validate([
            'message' => 'required|string|min:2|max:1000',
        ]);

        $conv = $this->ensureConversation($request);

        Message::create([
            'conversation_id' => $conv->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $result = $chatbot->handle([
            'question' => $data['message'],
            'conversation_id' => $conv->id,
            'context' => 'dashboard_pringsewu',
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'role' => 'assistant',
            'content' => $result['reply'],
        ]);

        if ($conv->title === 'Percakapan Baru') {
            $conv->title = mb_substr($data['message'], 0, 50);
            $conv->save();
        }

        return response()->json([
            'ok' => true,
            'reply' => $result['reply'],
        ]);
    }

    /* ===============================
     * DOWNLOAD PERCAKAPAN
     * =============================== */
    public function downloadConversation(Request $request, string $id)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::where('id', $id)
            ->where('owner_key', $ownerKey)
            ->firstOrFail();

        $messages = Message::where('conversation_id', $conv->id)->get();

        return response()->streamDownload(function () use ($conv, $messages) {
            echo "=== {$conv->title} ===\n\n";
            foreach ($messages as $m) {
                echo strtoupper($m->role) . ":\n{$m->content}\n\n";
            }
        }, 'conversation.txt');
    }
}
