<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable=[
        "name",
        "banner_image",
        "is_use",
        "province_id",
        "sort_order",
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function residences()
    {
        return $this->hasMany(Residence::class);
    }

    public function popularCity()
    {
        return $this->hasOne(PopularCity::class);
    }
}
