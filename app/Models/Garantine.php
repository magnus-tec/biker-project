<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantine extends Model
{
    use HasFactory;

    protected $table = 'garantines';

    protected $fillable = [
        'codigo',
        'drives_id',
        'marca',
        'modelo',
        'anio',
        'color',
        'nro_chasis',
        'nro_motor',
        'user_register',
        'user_update',
        'status',
        'fecha_registro',
        'fecha_actualizacion',
    ];
    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'status' => 'boolean',
    ];


    public function userRegistered()
    {
        return $this->belongsTo(User::class, 'user_register');
    }

    public function userUpdated()
    {
        return $this->belongsTo(User::class, 'user_update');
    }
    public function drive()
    {
        return $this->belongsTo(Drive::class, 'drives_id');
    }
}
