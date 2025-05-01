<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyItem extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','quantity','user_register','fecha_registro','price','status','buy_id'];
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->user_register = auth()->id();
        });
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
