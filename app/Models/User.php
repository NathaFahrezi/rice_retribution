<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi ke profile
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    // Relasi ke data penjualan
    public function masyarakat()
    {
        return $this->hasMany(Masyarakat::class, 'created_by');
    }

    // Relasi ke Polres melalui UserProfile
    public function polres()
    {
        return $this->hasOneThrough(
            \App\Models\Polres::class,
            \App\Models\UserProfile::class,
            'user_id',
            'id',
            'id',
            'polres_id'
        );
    }

    // Relasi ke Polsek melalui UserProfile
    public function polsek()
    {
        return $this->hasOneThrough(
            \App\Models\Polsek::class,
            \App\Models\UserProfile::class,
            'user_id',
            'id',
            'id',
            'polsek_id'
        );
    }

    public function userProfile()
{
    return $this->hasOne(UserProfile::class, 'user_id');
}

}
