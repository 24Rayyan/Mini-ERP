<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntertainmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'event_date',
        'location',
        'attendee_name',
        'attendee_company',
        'attendee_position',
        'purpose',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Relasi: EntertainmentDetail milik 1 Transaksi
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
