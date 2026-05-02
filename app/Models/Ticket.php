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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chats()
    {
        return $this->hasMany(TicketChat::class);
    }

    public function scopeSearch(Builder $query, $searchText){
        return $query
            ->orWhere('title', 'like', '%' . $searchText . '%')
            ->orWhere('id',   $searchText );
    }
}
