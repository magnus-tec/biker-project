<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
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
        'quotation_id',
        'serie',
        'number',
        'document_type_id',
        'companies_id',
        'payments_id',
        'districts_id',
        'mechanics_id',
        'status_sunat',
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
    public function saleItems()
    {
        return $this->hasMany(SalesItem::class, 'sale_id');
    }
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
    public function companies()
    {
        return $this->belongsTo(Company::class);
    }
    public function districts()
    {
        return $this->belongsTo(District::class);
    }
    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanics_id');
    }
}
