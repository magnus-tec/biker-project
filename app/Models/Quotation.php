<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
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
        'code',
        'igv',
        'status_sale',
        'document_type_id'

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
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
