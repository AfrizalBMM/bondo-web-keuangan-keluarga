<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Events\FamilyDataUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::where('family_id', $request->user()->family_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'type' => $asset->type,
                    'initialValue' => $asset->initial_value,
                    'purchaseDate' => $asset->purchase_date->format('Y-m-d'),
                    'depreciationRate' => $asset->depreciation_rate,
                ];
            });

        return Inertia::render('Assets', [
            'assets' => $assets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'initialValue' => 'required|numeric|min:0',
            'purchaseDate' => 'required|date',
            'depreciationRate' => 'required|numeric',
        ]);

        $request->user()->family->assets()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'initial_value' => $validated['initialValue'],
            'purchase_date' => $validated['purchaseDate'],
            'depreciation_rate' => $validated['depreciationRate'],
        ]);

        FamilyDataUpdated::dispatch($request->user()->family_id);

        return redirect()->back()->with('success', 'Aset berhasil ditambahkan');
    }
}
