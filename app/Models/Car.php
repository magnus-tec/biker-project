<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'drives_id',
        'codigo',
        'placa',
        'marca',
        'modelo',
        'anio',
        'numero_unidad',
        'condicion',
        'garantia',
        'nro_chasis',
        'nro_motor',
        'vehiculo_flota',
        'fecha_soat',
        'fecha_seguro',
        'color',
        'user_register',
        'user_update',
        'fecha_registro',
        'fecha_actualizacion',
        'status'
    ];

    public $timestamps = true;

    protected $casts = [
        'anio' => 'integer',
        'fecha_soat' => 'date',
        'fecha_seguro' => 'date',
        'fecha_registro' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'status' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Drive::class, 'drives_id');
    }

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
