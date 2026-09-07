<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}