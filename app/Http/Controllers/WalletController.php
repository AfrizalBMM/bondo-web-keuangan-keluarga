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
        $family = $request->user()->family;
        
        if (!$family) {
            return redirect()->route('onboarding')->with('warning', 'Silakan buat atau gabung keluarga terlebih dahulu.');
        }

        $wallets = $family->wallets()->orderBy('created_at', 'desc')->get()->map(function($wallet) {
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

    public function update(Request $request, Wallet $wallet)
    {
        if ($wallet->family_id !== $request->user()->family_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'balance' => 'required|numeric',
            'color' => 'required|string',
        ]);

        $wallet->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'color' => $validated['color'],
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Data dompet berhasil diperbarui');
    }

    public function destroy(Request $request, Wallet $wallet)
    {
        if ($wallet->family_id !== $request->user()->family_id) {
            abort(403);
        }

        // Optional: Check if the wallet has transactions to prevent deletion, or cascade delete.
        // Assuming cascade or simple deletion for now.
        $wallet->delete();

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Dompet berhasil dihapus');
    }
}
