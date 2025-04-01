<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationPaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'quotation_payment_method';

    protected $fillable = ['quotation_id', 'payment_method_id', 'amount', 'order'];
}
