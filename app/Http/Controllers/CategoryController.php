<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Events\FamilyDataUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('family_id', $request->user()->family_id)
            ->withCount('transactions as tx_count')
            ->withSum('transactions as total_amount', 'amount')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'type' => $cat->type === 'Income' ? 'Pemasukan' : 'Pengeluaran',
                    'color' => $cat->color,
                    'tx_count' => $cat->tx_count ?? 0,
                    'total_amount' => $cat->total_amount ?? 0,
                ];
            });

        return Inertia::render('Categories', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Pemasukan,Pengeluaran',
            'color' => 'required|string',
        ]);

        $request->user()->family->categories()->create([
            'name' => $validated['name'],
            'type' => $validated['type'] === 'Pemasukan' ? 'Income' : 'Expense',
            'color' => $validated['color'],
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }
}
