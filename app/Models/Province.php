<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'regions_id'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'regions_id');
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
