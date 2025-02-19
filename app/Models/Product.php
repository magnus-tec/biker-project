<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'bar_code',
        'description',
        'amount',
        'model',
        'location',
        'warehouse_id',
        'brand_id',
        'unit_id'
    ];

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    // Relationship: A Product belongs to a Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Relationship: A Product belongs to a Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
