<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buy extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_price',
        'status',
        'user_register',
        'fecha_registro',
        'user_update',
        'fecha_actualizacion',
        'customer_dni',
        'customer_names_surnames',
        'customer_address',
        'code',
        'igv',
        'status_sale',
        'document_type_id',
        'fecha_vencimiento',
        'serie',
        'number'

    ];


    protected static function booted()
    {
        static::creating(function ($model) {
            $model->user_register = auth()->id();
        });
        static::updating(function ($model) {
            $model->user_update = auth()->id();
        });
    }

    public function userRegister()
    {
        return $this->belongsTo(User::class, 'user_register');
    }
    public function buyItems()
    {
        return $this->hasMany(BuyItem::class, 'buy_id');
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
