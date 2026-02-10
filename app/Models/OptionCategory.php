<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionCategory extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "type"
    ];
    function options(){
        return $this->hasMany(Option::class,"option_category_id","id");
    }
}
