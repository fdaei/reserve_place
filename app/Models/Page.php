<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'url_text',
        'title',
        'text',
        'status',
        'visit_count',
    ];

    public function category()
    {
        return $this->belongsTo(PageCategory::class, 'category_id');
    }
}
