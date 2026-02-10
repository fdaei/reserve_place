<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $fillable=[
        "url_text",
        "title",
        "text",
        "status",
        "visit_count",
    ];
}
