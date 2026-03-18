<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\FamilyDataUpdated;
use Inertia\Inertia;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        
        $debts = Debt::where('family_id', $familyId)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($debt) {
                return [
                    'id' => $debt->id,
                    'type' => $debt->type,
                    'counterparty' => $debt->counterparty,
                    'totalAmount' => $debt->total_amount,
                    'paidAmount' => $debt->paid_amount,
                    'dueDate' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                    'status' => $debt->status,
                    'notes' => $debt->notes,
                ];
            });

        $wallets = Wallet::where('family_id', $familyId)->select('id', 'name', 'balance', 'color')->get();

        return Inertia::render('Debts', [
            'debts' => $debts,
            'wallets' => $wallets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Hutang,Piutang',
            'counterparty' => 'required|string|max:255',
            'totalAmount' => 'required|numeric|min:1',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $familyId = $request->user()->family_id;

        $request->user()->family->debts()->create([
            'type' => $validated['type'],
            'counterparty' => $validated['counterparty'],
            'total_amount' => $validated['totalAmount'],
            'paid_amount' => 0,
            'due_date' => $validated['dueDate'],
            'status' => 'ongoing',
            'notes' => $validated['notes'] ?? '',
        ]);

        FamilyDataUpdated::dispatch($familyId);

        return redirect()->back()->with('success', 'Catatan Hutang/Piutang berhasil ditambahkan');
    }

    public function payment(Request $request, Debt $debt)
    {
        $familyId = $request->user()->family_id;
        
        if ($debt->family_id !== $familyId) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_id' => 'required|exists:wallets,id',
        ]);
        
        $wallet = Wallet::where('id', $validated['wallet_id'])
                        ->where('family_id', $familyId)
                        ->firstOrFail();

        DB::transaction(function () use ($debt, $validated, $wallet, $familyId, $request) {
            $debt->paid_amount += $validated['amount'];
            
            if ($debt->paid_amount >= $debt->total_amount) {
                $debt->status = 'completed';
            }
            $debt->save();
            
            // Create a System Category if not exists
            $categoryName = $debt->type === 'Hutang' ? 'Pembayaran Hutang' : 'Penerimaan Piutang';
            $categoryType = $debt->type === 'Hutang' ? 'Expense' : 'Income';
            
            $category = Category::firstOrCreate(
                ['family_id' => $familyId, 'name' => $categoryName],
                ['type' => $categoryType, 'color' => ($debt->type === 'Hutang' ? 'rose' : 'emerald')]
            );

            // Record Transaction
            Transaction::create([
                'family_id' => $familyId,
                'wallet_id' => $wallet->id,
                'category_id' => $category->id,
                'user_id' => $request->user()->id,
                'type' => $categoryType,
                'amount' => $validated['amount'],
                'date' => now(),
                'notes' => 'Cicilan ' . $debt->type . ': ' . $debt->counterparty,
            ]);

            // Mutate Wallet Balance
            if ($categoryType === 'Income') {
                $wallet->balance += $validated['amount'];
            } else {
                $wallet->balance -= $validated['amount'];
            }
            $wallet->save();
        });

        FamilyDataUpdated::dispatch($familyId);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat');
    }

    public function update(Request $request, Debt $debt)
    {
        if ($debt->family_id !== $request->user()->family_id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:Hutang,Piutang',
            'counterparty' => 'required|string|max:255',
            'totalAmount' => 'required|numeric|min:1',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $debt->update([
            'type' => $validated['type'],
            'counterparty' => $validated['counterparty'],
            'total_amount' => $validated['totalAmount'],
            'due_date' => $validated['dueDate'],
            'notes' => $validated['notes'] ?? '',
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Catatan Hutang/Piutang berhasil diperbarui');
    }

    public function destroy(Request $request, Debt $debt)
    {
        if ($debt->family_id !== $request->user()->family_id) {
            abort(403);
        }

        $debt->delete();

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Catatan Hutang/Piutang berhasil dihapus');
    }
}
