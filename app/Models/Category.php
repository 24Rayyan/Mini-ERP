<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_tax_deductible',
        'description',
    ];

    protected $casts = [
        'is_tax_deductible' => 'boolean',
    ];

    /**
     * Relasi: 1 Kategori memiliki banyak Transaksi
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope untuk kategori pendapatan (income)
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope untuk kategori pengeluaran (expense)
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope untuk pengeluaran yang dapat dikurangkan dari pajak (Deductible)
     */
    public function scopeTaxDeductible($query)
    {
        return $query->where('is_tax_deductible', true);
    }

    /**
     * Scope untuk pengeluaran non-deductible (Koreksi Fiskal Positif)
     */
    public function scopeNonDeductible($query)
    {
        return $query->where('is_tax_deductible', false);
    }

    /**
     * Display label format: [5-101] Biaya ATK
     */
    public function getFullDisplayNameAttribute(): string
    {
        return "[{$this->code}] {$this->name}";
    }
}
