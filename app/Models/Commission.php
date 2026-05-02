<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'host_id',
        'rate',
        'amount',
        'host_share_amount',
        'status',
        'settled_at',
        'notes',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'amount' => 'integer',
        'host_share_amount' => 'integer',
        'settled_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $builder) use ($search) {
            $builder->where('id', $search)
                ->orWhereHas('booking', fn (Builder $booking) => $booking->where('booking_number', 'like', '%'.$search.'%'))
                ->orWhereHas('host', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
        });
    }
}
