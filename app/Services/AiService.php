<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function parseTransaction($text)
    {
        $apiKey = config('services.groq.key');
        $user = auth()->user();
        
        // Prompt yang lebih kompleks untuk deteksi mendalam
        $prompt = "Tugas: Ekstrak data transaksi dari teks: '$text'.
        Aturan:
        1. 'type': 'expense' (pengeluaran), 'income' (pemasukan), atau 'transfer'.
        2. 'amount': nominal angka murni.
        3. 'category': Kategori yang relevan (misal: Makan, Transport, Gaji, Hiburan).
        4. 'wallet': Deteksi sumber dana (misal: Tunai, BCA, ShopeePay). Default: 'Tunai'.
        5. 'actor': Deteksi siapa (Suami/Istri) jika disebutkan. Jika tidak, anggap '{$user->name}'.
        6. 'date': Gunakan format YYYY-MM-DD. Hari ini: " . now()->format('Y-m-d') . ".

        Berikan HANYA JSON mentah:";

        $response = Http::withToken($apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

        return $response->json()['choices'][0]['message']['content'] ?? null;
    }
}