<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'drives_id',
        'cars_id',
        'users_id',
        'descripcion',
        'user_register',
        'user_update',
        'status',
        'status_service',
        'fecha_registro',
        'fecha_actualizacion',
        'codigo',
        'detalle_servicio'
    ];

    public $timestamps = true;

    /**
     * Relación con el modelo Drive
     */
    public function drive()
    {
        return $this->belongsTo(Drive::class, 'drives_id');
    }

    /**
     * Relación con el modelo Car
     */
    public function car()
    {
        return $this->belongsTo(Car::class, 'cars_id');
    }

    /**
     * Relación con el usuario que registró el servicio
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'user_register');
    }

    /**
     * Relación con el usuario que actualizó el servicio
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'user_update');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
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
