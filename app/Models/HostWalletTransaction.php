<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'booking_id',
        'type',
        'amount',
        'balance_after',
        'status',
        'description',
        'reference_number',
        'available_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'available_at' => 'datetime',
        'meta' => 'array',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $builder) use ($search) {
            $builder->where('reference_number', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('host', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
        });
    }
}
