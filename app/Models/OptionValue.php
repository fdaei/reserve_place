<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    use HasFactory;
    protected $fillable=[
        "option_id",
        "residence_id",
        "foodstore_id",
        "friend_id",
        "value"
    ];
    function option(){
        return $this->hasOne(Option::class,"id","option_id");
    }
}
