<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';
    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'unit_price',
        'item_type',
        'mechanics_id'
    ];

    public function sales()
    {
        return $this->belongsTo(Sale::class);
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
