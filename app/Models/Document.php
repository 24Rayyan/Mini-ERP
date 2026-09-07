<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'document_number',
        'date',
        'status',
        'total_amount',
        'discount',
        'tax_transaction_code',
        'tax_invoice_date',
        'payment_note',
    ];

    // Relasi: 1 Document punya banyak DocumentItem
    public function items()
    {
        return $this->hasMany(DocumentItem::class);
    }

    // Relasi: 1 Document milik 1 Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: 1 Document (Invoice) terhubung ke 1 Transaksi Keuangan
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'invoice_id');
    }
}