<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable=[
        "area",
        "title",
        "status",
        "user_id"
    ];
    public function scopeSearch(Builder $query, $searchText){
        return $query
            ->orWhere('title', 'like', '%' . $searchText . '%')
            ->orWhere('id',   $searchText );
    }
}
