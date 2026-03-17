<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\FamilyDataUpdated;
use Inertia\Inertia;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        
        $goals = Goal::where('family_id', $familyId)
            ->orderBy('target_date', 'asc')
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'targetAmount' => $goal->target_amount,
                    'currentAmount' => $goal->current_amount,
                    'targetDate' => $goal->target_date ? $goal->target_date->format('Y-m-d') : null,
                    'color' => $goal->color,
                ];
            });

        $wallets = Wallet::where('family_id', $familyId)->select('id', 'name', 'balance', 'color')->get();

        return Inertia::render('Goals', [
            'goals' => $goals,
            'wallets' => $wallets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'targetAmount' => 'required|numeric|min:1',
            'targetDate' => 'required|date',
            'color' => 'required|string',
        ]);

        $request->user()->family->goals()->create([
            'name' => $validated['name'],
            'target_amount' => $validated['targetAmount'],
            'current_amount' => 0,
            'target_date' => $validated['targetDate'],
            'color' => $validated['color'],
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Target keuangan berhasil dibuat');
    }

    public function deposit(Request $request, Goal $goal)
    {
        $familyId = $request->user()->family_id;
        
        if ($goal->family_id !== $familyId) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_id' => 'required|exists:wallets,id',
        ]);
        
        $wallet = Wallet::where('id', $validated['wallet_id'])
                        ->where('family_id', $familyId)
                        ->firstOrFail();

        DB::transaction(function () use ($goal, $validated, $wallet, $familyId, $request) {
            $goal->current_amount += $validated['amount'];
            $goal->save();
            
            // Create a System Category if not exists
            $category = Category::firstOrCreate(
                ['family_id' => $familyId, 'name' => 'Tabungan Goal'],
                ['type' => 'Expense', 'color' => 'blue']
            );

            // Record Transaction
            Transaction::create([
                'family_id' => $familyId,
                'wallet_id' => $wallet->id,
                'category_id' => $category->id,
                'user_id' => $request->user()->id,
                'type' => 'Expense', // Treat depositing to goal as an expense from wallet
                'amount' => $validated['amount'],
                'date' => now(),
                'notes' => 'Setoran ke: ' . $goal->name,
            ]);

            // Deduct from Wallet
            $wallet->balance -= $validated['amount'];
            $wallet->save();
        });

        FamilyDataUpdated::dispatch($familyId);

        return redirect()->back()->with('success', 'Setoran berhasil ditambahkan ke target');
    }
}
