<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'name',
        'type',
        'initial_value',
        'purchase_date',
        'depreciation_rate',
    ];

    protected $casts = [
        'initial_value' => 'decimal:2',
        'purchase_date' => 'date',
        'depreciation_rate' => 'decimal:2',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}
