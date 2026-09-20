<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_has_modules');
    }

    // Helper untuk mengecek apakah role punya akses ke module key tertentu
    public function hasModule($moduleKey): bool
    {
        return $this->modules->pluck('key')->contains($moduleKey);
    }
}