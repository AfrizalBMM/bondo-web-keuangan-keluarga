<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Category;
use App\Services\GeminiApiService;
use App\Events\FamilyDataUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;

        // Fetch paginated transactions
        $transactions = Transaction::where('family_id', $familyId)
            ->with(['wallet', 'category', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    'id' => $trx->id,
                    'text' => $trx->notes ?: ($trx->category ? $trx->category->name : 'Manual Input'),
                    'category' => $trx->category ? $trx->category->name : '-',
                    'type' => strtolower($trx->type), // 'income' or 'expense'
                    'wallet' => $trx->wallet ? $trx->wallet->name : '-',
                    'wallet_color' => $trx->wallet ? $trx->wallet->color : 'bg-slate-400',
                    'amount' => $trx->amount,
                    'date' => $trx->date->format('d M Y, H:i'),
                ];
            });

        // Fetch data for dropdowns
        $wallets = Wallet::where('family_id', $familyId)->select('id', 'name', 'color')->get();
        $categories = Category::where('family_id', $familyId)->select('id', 'name', 'type')->get();

        return Inertia::render('Transactions', [
            'transactions' => $transactions,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:Income,Expense',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::where('id', $validated['wallet_id'])
                        ->where('family_id', $request->user()->family_id)
                        ->firstOrFail();

        DB::transaction(function () use ($validated, $request, $wallet) {
            // Create Transaction
            Transaction::create([
                'family_id' => $request->user()->family_id,
                'wallet_id' => $wallet->id,
                'category_id' => $validated['category_id'],
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            // Mutate balance
            if ($validated['type'] === 'Income') {
                $wallet->balance += $validated['amount'];
            } else {
                $wallet->balance -= $validated['amount'];
            }
            $wallet->save();
        });

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan');
    }

    public function smartStore(Request $request, GeminiApiService $geminiService)
    {
        $validated = $request->validate([
            'smart_input' => 'required|string|max:500',
        ]);

        $familyId = $request->user()->family_id;
        $wallets = Wallet::where('family_id', $familyId)->select('id', 'name')->get();
        $categories = Category::where('family_id', $familyId)->select('id', 'name')->get();

        if ($wallets->isEmpty() || $categories->isEmpty()) {
            return redirect()->back()->withErrors(['smart_input' => 'Harap buat Dompet dan Kategori terlebih dahulu sebelum menggunakan Smart Add.']);
        }

        $parsedData = $geminiService->parseTransaction($validated['smart_input'], $wallets, $categories);

        if (!$parsedData || !isset($parsedData['amount'], $parsedData['wallet_id'], $parsedData['category_id'], $parsedData['type'])) {
            return redirect()->back()->withErrors(['smart_input' => 'AI gagal memahami transaksi. Coba kalimat yang lebih spesifik (misal: "Beli cilok 5rb tunai").']);
        }

        $wallet = Wallet::where('id', $parsedData['wallet_id'])
                        ->where('family_id', $familyId)
                        ->first() ?? $wallets->first(); // fallback to first wallet if ID mismatch

        DB::transaction(function () use ($parsedData, $request, $wallet) {
            Transaction::create([
                'family_id' => $request->user()->family_id,
                'wallet_id' => $wallet->id,
                'category_id' => $parsedData['category_id'],
                'user_id' => $request->user()->id,
                'type' => $parsedData['type'],
                'amount' => $parsedData['amount'],
                'date' => $parsedData['date'] ?? now()->format('Y-m-d'),
                'notes' => $parsedData['notes'] ?? 'Smart Add',
            ]);

            if ($parsedData['type'] === 'Income') {
                $wallet->balance += $parsedData['amount'];
            } else {
                $wallet->balance -= $parsedData['amount'];
            }
            $wallet->save();
        });

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Smart Add berhasil dicatat!');
    }
}
