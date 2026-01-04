<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    private function ownerKey(Request $request): string
    {
        // identitas user tanpa login: tersimpan di session
        if (!$request->session()->has('owner_key')) {
            $request->session()->put('owner_key', sha1((string) Str::uuid()));
        }
        return (string) $request->session()->get('owner_key');
    }

    private function ensureConversation(Request $request): Conversation
    {
        $ownerKey = $this->ownerKey($request);

        // jika session belum punya conv_id, buat baru
        $convId = $request->session()->get('conv_id');
        if (!$convId) {
            $conv = Conversation::create([
                'id' => (string) Str::uuid(),
                'owner_key' => $ownerKey,
                'title' => 'Percakapan Baru',
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
            $request->session()->put('conv_id', $conv->id);
            return $conv;
        }

        $conv = Conversation::where('id', $convId)
            ->where('owner_key', $ownerKey)
            ->first();

        if (!$conv) {
            // kalau conv_id invalid, buat baru
            $conv = Conversation::create([
                'id' => (string) Str::uuid(),
                'owner_key' => $ownerKey,
                'title' => 'Percakapan Baru',
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
            $request->session()->put('conv_id', $conv->id);
        }

        return $conv;
    }

    public function index(Request $request)
    {
        $ownerKey = $this->ownerKey($request);
        $current = $this->ensureConversation($request);

        $conversations = Conversation::where('owner_key', $ownerKey)
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        $messages = Message::where('conversation_id', $current->id)
            ->orderBy('id')
            ->get();

        return view('chat', [
            'conversations' => $conversations,
            'currentConversation' => $current,
            'messages' => $messages,
        ]);
    }

    public function newConversation(Request $request)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::create([
            'id' => (string) Str::uuid(),
            'owner_key' => $ownerKey,
            'title' => 'Percakapan Baru',
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $request->session()->put('conv_id', $conv->id);

        return response()->json([
            'ok' => true,
            'conversation_id' => $conv->id,
        ]);
    }

    public function openConversation(Request $request, string $id)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::where('id', $id)
            ->where('owner_key', $ownerKey)
            ->firstOrFail();

        $request->session()->put('conv_id', $conv->id);

        $conversations = Conversation::where('owner_key', $ownerKey)
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        $messages = Message::where('conversation_id', $conv->id)
            ->orderBy('id')
            ->get();

        return view('chat', [
            'conversations' => $conversations,
            'currentConversation' => $conv,
            'messages' => $messages,
        ]);
    }

    public function chat(Request $request, ChatbotService $chatbot)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $message = trim($validated['message']);
        $ownerKey = $this->ownerKey($request);
        $conv = $this->ensureConversation($request);

        // simple anti prompt-injection
        $lower = mb_strtolower($message);
        if (str_contains($lower, 'abaikan instruksi') || str_contains($lower, 'ignore previous')) {
            return response()->json([
                'ok' => false,
                'reply' => 'Maaf, permintaan itu tidak dapat diproses.',
            ], 400);
        }

        // simpan pesan user
        Message::create([
            'conversation_id' => $conv->id,
            'role' => 'user',
            'content' => $message,
        ]);

        // panggil AI
        $result = $chatbot->handle($message, $conv->id, $request->ip());

        // simpan jawaban AI
        Message::create([
            'conversation_id' => $conv->id,
            'role' => 'assistant',
            'content' => $result['reply'],
        ]);

        // update judul otomatis dari pesan pertama (singkat)
        if ($conv->title === 'Percakapan Baru') {
            $title = mb_substr($message, 0, 50);
            $conv->title = $title;
        }
        $conv->touch(); // update updated_at
        $conv->save();

        return response()->json([
            'ok' => true,
            'reply' => $result['reply'],
            'meta' => $result['meta'],
            'conversation_id' => $conv->id,
        ]);
    }

    public function downloadConversation(Request $request, string $id)
    {
        $ownerKey = $this->ownerKey($request);

        $conv = Conversation::where('id', $id)
            ->where('owner_key', $ownerKey)
            ->firstOrFail();

        $messages = Message::where('conversation_id', $conv->id)
            ->orderBy('id')
            ->get(['role', 'content', 'created_at']);

        $format = strtolower((string) $request->query('format', 'txt'));
        if (!in_array($format, ['txt', 'json', 'csv'], true)) {
            $format = 'txt';
        }

        $safeTitle = preg_replace('/[^a-zA-Z0-9\-_]+/', '_', $conv->title ?: 'conversation');
        $filename = $safeTitle . '_' . $conv->id . '.' . $format;

        if ($format === 'json') {
            return response()->streamDownload(function () use ($conv, $messages) {
                echo json_encode([
                    'conversation' => [
                        'id' => $conv->id,
                        'title' => $conv->title,
                        'created_at' => $conv->created_at,
                        'updated_at' => $conv->updated_at,
                    ],
                    'messages' => $messages,
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
        }

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($messages) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['role', 'content', 'created_at']);
                foreach ($messages as $m) {
                    fputcsv($out, [$m->role, $m->content, (string) $m->created_at]);
                }
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        // txt
        return response()->streamDownload(function () use ($conv, $messages) {
            echo "=== " . ($conv->title ?: 'Percakapan') . " ===\n";
            echo "ID: {$conv->id}\n";
            echo "Dibuat: {$conv->created_at}\n";
            echo "Diupdate: {$conv->updated_at}\n";
            echo "==============================\n\n";

            foreach ($messages as $m) {
                $who = $m->role === 'user' ? 'USER' : 'AI';
                echo "[{$who}] ({$m->created_at})\n";
                echo $m->content . "\n\n";
            }
        }, $filename, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
