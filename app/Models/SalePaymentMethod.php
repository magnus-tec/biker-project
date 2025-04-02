<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'sale_payment_method';

    protected $fillable = ['sale_id', 'payment_method_id', 'amount', 'order'];
}
