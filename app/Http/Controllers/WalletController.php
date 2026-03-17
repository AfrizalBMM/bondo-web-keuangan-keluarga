<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Events\FamilyDataUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = $request->user()->family->wallets()->orderBy('created_at', 'desc')->get()->map(function($wallet) {
            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'type' => $wallet->type,
                'balance' => $wallet->balance,
                'color' => $wallet->color,
                'last_updated' => $wallet->updated_at->diffForHumans(),
            ];
        });

        return Inertia::render('Wallets', [
            'wallets' => $wallets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'initialBalance' => 'required|numeric',
            'color' => 'required|string',
        ]);

        $request->user()->family->wallets()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'starting_balance' => $validated['initialBalance'],
            'balance' => $validated['initialBalance'],
            'color' => $validated['color'],
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Dompet berhasil ditambahkan');
    }
}
