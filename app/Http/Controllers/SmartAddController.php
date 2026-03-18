<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;

class SmartAddController extends Controller
{

    public function process(Request $request, AiService $aiService)
    {
        $request->validate(['text' => 'required|string']);
        
        $result = $aiService->parseTransaction($request->text);
        $data = json_decode($result, true);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'AI gagal memproses teks'], 422);
        }

        // LOGIKA PENTING: Cari ID Kategori & Wallet berdasarkan nama dari AI
        $category = \App\Models\Category::where('name', 'like', '%' . $data['category'] . '%')->first();
        $wallet = \App\Models\Wallet::where('name', 'like', '%' . $data['wallet'] . '%')->first();

        // Simpan ke Database
        $transaction = \App\Models\Transaction::create([
            'user_id'     => auth()->id(),
            'family_id'   => auth()->user()->family_id,
            'type'        => $data['type'] ?? 'expense',
            'amount'      => (int) $data['amount'],
            'category_id' => $category ? $category->id : 1, // Default ke ID 1 jika tidak ketemu
            'wallet_id'   => $wallet ? $wallet->id : 1,     // Default ke ID 1 jika tidak ketemu
            'description' => $data['description'] ?? $request->text,
            'date'        => $data['date'] ?? now()->format('Y-m-d'),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $transaction
        ]);
    }
}