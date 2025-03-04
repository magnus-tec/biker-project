<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
