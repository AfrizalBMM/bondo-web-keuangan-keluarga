<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $family = $request->user()->family;

        if (!$family) {
            return redirect()->route('onboarding')->with('warning', 'Silakan buat atau gabung keluarga terlebih dahulu.');
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get categories to populate the dropdown
        $categories = Category::where('family_id', $family->id)
            ->where('type', 'Expense')
            ->get();

        // Get budgets with spent amount calculated
        $budgets = Budget::with('category')
            ->where('family_id', $family->id)
            ->get()
            ->map(function ($budget) use ($family, $startOfMonth, $endOfMonth) {
                // Calculate spent amount for the current month
                $spentAmount = \App\Models\Transaction::where('family_id', $family->id)
                    ->where('category_id', $budget->category_id)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                return [
                    'id' => $budget->id,
                    'category' => $budget->category ? $budget->category->name : 'Unknown',
                    'category_id' => $budget->category_id,
                    'limitAmount' => $budget->limit_amount,
                    'spentAmount' => $spentAmount,
                    'color' => $budget->category ? $budget->category->color : 'bg-slate-500', 
                ];
            });

        return Inertia::render('Budget', [
            'budgets' => $budgets,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'limitAmount' => 'required|numeric|min:0',
        ]);

        $family = $request->user()->family;

        // Check if category belongs to family
        $category = Category::where('id', $validated['category_id'])
            ->where('family_id', $family->id)
            ->firstOrFail();

        Budget::updateOrCreate(
            ['family_id' => $family->id, 'category_id' => $validated['category_id']],
            ['limit_amount' => $validated['limitAmount']]
        );

        \App\Events\FamilyDataUpdated::dispatch($family->id);

        return redirect()->back()->with('success', 'Anggaran berhasil disimpan');
    }

    public function destroy(Request $request, Budget $budget)
    {
        if ($budget->family_id !== $request->user()->family_id) {
            abort(403);
        }

        $budget->delete();

        \App\Events\FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Anggaran bulanan berhasil dihapus');
    }
}
