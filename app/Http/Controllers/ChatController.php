<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ChatbotService;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        // session conversation id untuk membedakan user (tanpa login)
        if (!$request->session()->has('conv_id')) {
            $request->session()->put('conv_id', (string) Str::uuid());
        }

        return view('chat');
    }

    public function chat(Request $request, ChatbotService $chatbot)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $message = trim($validated['message']);

        // anti prompt-injection sederhana: batasi kata-kata yang menyuruh “abaikan instruksi”
        // (bukan jaminan, tapi membantu)
        $lower = mb_strtolower($message);
        if (str_contains($lower, 'abaikan instruksi') || str_contains($lower, 'ignore previous')) {
            return response()->json([
                'ok' => false,
                'reply' => 'Maaf, permintaan itu tidak dapat diproses.',
            ], 400);
        }

        $convId = (string) $request->session()->get('conv_id');
        $ip = $request->ip();

        $result = $chatbot->handle($message, $convId, $ip);

        return response()->json([
            'ok' => true,
            'reply' => $result['reply'],
            'meta' => $result['meta'],
        ]);
    }
}
