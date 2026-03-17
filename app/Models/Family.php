<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'invite_code',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
