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
        'customer_address',
        'code',
        'igv',
        'status_sale',
        'document_type_id',
        'payment_method_id',
        'companies_id',
        'payments_id',
        'districts_id',
        'mechanics_id',
        'nro_dias',
        'fecha_vencimiento',

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
    public function quotationPaymentMethod()
    {
        return $this->hasMany(QuotationPaymentMethod::class, 'quotation_id');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'districts_id');
    }
    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanics_id');
    }
    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }
}
