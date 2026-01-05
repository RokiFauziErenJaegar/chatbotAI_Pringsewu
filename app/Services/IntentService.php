<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IntentService
{
    public function detect(string $question): array
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Klasifikasikan maksud pertanyaan ke dalam: average, comparison, trend, ranking, summary'
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'temperature' => 0
            ]);

        $intent = strtolower($response['choices'][0]['message']['content']);

        return [
            'intent' => trim($intent)
        ];
    }
}
