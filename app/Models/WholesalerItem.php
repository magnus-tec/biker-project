<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesalerItem extends Model
{
    use HasFactory;
    protected $table = 'wholesaler_items';
    protected $fillable = [
        'wholesaler_id',
        'item_id',
        'quantity',
        'unit_price',
        'item_type',
        'item_name',
        'product_prices_id',
        'users_id',
        'mechanics_id'
    ];

    public function wholesaler()
    {
        return $this->belongsTo(Wholesaler::class);
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
