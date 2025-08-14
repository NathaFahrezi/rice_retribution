<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Polres;
use App\Models\Polsek;

class Masyarakat extends Model
{
    protected $table = 'masyarakat';

    protected $fillable = [
        'polres_id',
        'polsek_id',
        'created_by',
        'foto_ktp',
        'jumlah_beras',
    ];

    // Relasi ke user yang membuat data
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Polres
    public function polres()
    {
        return $this->belongsTo(Polres::class, 'polres_id');
    }

    // Relasi ke Polsek
    public function polsek()
    {
        return $this->belongsTo(Polsek::class, 'polsek_id');
    }
}
