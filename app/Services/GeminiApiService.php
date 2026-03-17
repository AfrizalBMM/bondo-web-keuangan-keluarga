<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeminiApiService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent';
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function parseTransaction($inputText, $wallets, $categories)
    {
        if (empty($this->apiKey)) {
            Log::warning('GEMINI_API_KEY is not set in environment variables.');
            return null;
        }

        $walletList = json_encode($wallets->mapWithKeys(fn($w) => [$w->id => $w->name])->toArray());
        $categoryList = json_encode($categories->mapWithKeys(fn($c) => [$c->id => $c->name])->toArray());
        
        $today = Carbon::now()->format('Y-m-d');

        $prompt = "You are a financial assistant for an Indonesian family finance app. Your job is to extract transaction details from raw text and return ONLY a valid JSON object. Do not wrap it in markdown block quotes.\n\n"
                . "Available Wallets (JSON map of ID to Name): {$walletList}\n"
                . "Available Categories (JSON map of ID to Name): {$categoryList}\n"
                . "Today's Date: {$today}\n\n"
                . "Extract the following from this text: \"{$inputText}\"\n\n"
                . "Required JSON keys in output:\n"
                . "- 'type': strictly 'Income' or 'Expense'\n"
                . "- 'amount': strictly numeric integer value (understand Indonesian abbreviations like 5rb = 5000, 1jt = 1000000)\n"
                . "- 'wallet_id': The ID of the wallet that matches the text. Default to the first wallet ID if unsure.\n"
                . "- 'category_id': The ID of the category that best matches. Default to the first applicable category ID if unsure.\n"
                . "- 'date': The date of the transaction in Y-m-d format. If not mentioned, use Today's Date.\n"
                . "- 'notes': A concise description or title derived from the text (e.g., 'Beli cilok' or 'Gajian')\n\n"
                . "Important: Return ONLY the raw JSON string. Do not include any explanations.";

        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1, // Low temperature for deterministic output
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                
                // Clean up any stray markdown wrappers just in case
                $content = preg_replace('/```json/i', '', $content);
                $content = preg_replace('/```/', '', $content);
                $content = trim($content);

                return json_decode($content, true);
            }
            
            Log::error('Gemini API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
