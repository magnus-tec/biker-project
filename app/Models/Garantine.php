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
        'nro_documento',
        'nombres_apellidos',
        'tipo_doc',
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
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->user_register = auth()->id();
        });
        static::updating(function ($model) {
            $model->user_update = auth()->id();
        });
    }
}
