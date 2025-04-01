<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;
    protected $table = 'quotation_items';
    protected $fillable = [
        'quotation_id',
        'item_id',
        'quantity',
        'unit_price',
        'item_type',
        'item_name',
        'product_prices_id',
        'users_id',
        'mechanics_id'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function service()
    {
        return $this->belongsTo(ServiceSale::class);
    }
    public function item()
    {
        return $this->morphTo();
    }
}
