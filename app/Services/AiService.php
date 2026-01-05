<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function analyze(string $question, $data): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah analis data pemerintahan Kabupaten Pringsewu. Jawab singkat, jelas, berbasis data.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Pertanyaan: $question\n\nData:\n" . json_encode($data)
                    ]
                ],
                'temperature' => 0.3
            ]);

        return $response['choices'][0]['message']['content'];
    }
}
