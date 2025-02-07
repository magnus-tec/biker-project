<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drive extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_doc',
        'nro_documento',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'nacionalidad',
        'nro_licencia',
        'categoria_licencia',
        'fecha_nacimiento',
        'telefono',
        'correo',
        'foto',
        'numeroCodFi',
        'numUnidad',
        'departamento',
        'provincia',
        'distrito',
        'direccion_detalle',
        'user_register',
        'nombres_contacto',
        'telefono_contacto',
        'parentesco_contacto',
        'user_update',
        'fecha_registro',
        'fecha_actualizacion',
        'status',
        'codigo'
    ];

    public $timestamps = true;

    protected $casts = [
        'fecha_nacimiento' => 'date',
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
}
