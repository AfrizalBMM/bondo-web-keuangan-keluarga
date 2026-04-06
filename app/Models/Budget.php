<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'category_id',
        'limit_amount',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spent()
    {
        return Transaction::where('family_id', $this->family_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'Expense')
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
    }
}
