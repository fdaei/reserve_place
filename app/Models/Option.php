<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "show_filter",
        "icon",
        "type",
        "option_category_id",
    ];


}
