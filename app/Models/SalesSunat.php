<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesSunat extends Model
{
    use HasFactory;

    protected $table = 'sales_sunat';

    protected $fillable = [
        'sale_id',
        'name_xml',
        'qr_info',
        'hash',
    ];
}
