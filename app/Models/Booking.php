<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_request_id',
        'customer_id',
        'host_id',
        'bookable_type',
        'bookable_id',
        'booking_number',
        'starts_at',
        'ends_at',
        'guests_count',
        'subtotal',
        'discount_amount',
        'commission_amount',
        'host_share_amount',
        'total_amount',
        'status',
        'payment_status',
        'settlement_status',
        'paid_at',
        'released_at',
        'settled_at',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'guests_count' => 'integer',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'commission_amount' => 'integer',
        'host_share_amount' => 'integer',
        'total_amount' => 'integer',
        'released_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class, 'booking_request_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function walletTransaction(): HasOne
    {
        return $this->hasOne(HostWalletTransaction::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $inner) use ($search) {
                $inner->where('booking_number', 'like', '%'.$search.'%')
                    ->orWhere('id', $search)
                    ->orWhereHas('customer', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'))
                    ->orWhereHas('host', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
            });
        });
    }

    public static function statuses(): array
    {
        return [
            'pending_host' => 'در انتظار تأیید میزبان',
            'awaiting_payment' => 'تأیید میزبان - منتظر پرداخت',
            'paid' => 'پرداخت شده - منتظر شروع اقامت',
            'staying' => 'در حال اقامت',
            'completed' => 'پایان اقامت - آماده تسویه',
            'settled' => 'تسویه شده با میزبان',
            'cancelled' => 'لغو شده',
            'rejected' => 'رد شده توسط میزبان',
        ];
    }
}
