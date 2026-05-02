<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'customer_id',
        'host_id',
        'assigned_to',
        'bookable_type',
        'bookable_id',
        'guest_name',
        'guest_phone',
        'starts_at',
        'ends_at',
        'guests_count',
        'total_amount',
        'commission_amount',
        'host_share_amount',
        'status',
        'host_approval_status',
        'payment_status',
        'stay_status',
        'settlement_status',
        'notes',
        'rejected_reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'guests_count' => 'integer',
        'total_amount' => 'integer',
        'commission_amount' => 'integer',
        'host_share_amount' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $inner) use ($search) {
                $inner->where('guest_name', 'like', '%'.$search.'%')
                    ->orWhere('guest_phone', 'like', '%'.$search.'%')
                    ->orWhere('id', $search)
                    ->orWhereHas('customer', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'))
                    ->orWhereHas('host', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
            });
        });
    }

    public static function statuses(): array
    {
        return [
            'pending' => 'در انتظار تأیید میزبان',
            'approved' => 'تأیید میزبان - منتظر پرداخت',
            'paid' => 'پرداخت شده',
            'staying' => 'در حال اقامت',
            'ended' => 'پایان اقامت',
            'releasable' => 'قابل تسویه',
            'settled' => 'تسویه شده',
            'cancelled' => 'لغو شده',
            'rejected' => 'رد شده توسط میزبان',
        ];
    }

    public static function hostApprovalStatuses(): array
    {
        return [
            'pending' => 'در انتظار تأیید میزبان',
            'approved' => 'تأیید شده توسط میزبان',
            'manual_approved' => 'تأیید دستی ادمین',
            'rejected' => 'رد شده توسط میزبان',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'unpaid' => 'منتظر پرداخت',
            'paid' => 'پرداخت شده',
            'failed' => 'خطای پرداخت',
            'refunded' => 'مسترد شده',
        ];
    }

    public static function stayStatuses(): array
    {
        return [
            'not_started' => 'منتظر شروع اقامت',
            'staying' => 'در حال اقامت',
            'ended' => 'پایان اقامت',
        ];
    }

    public static function settlementStatuses(): array
    {
        return [
            'pending' => 'در انتظار پایان اقامت',
            'blocked' => 'مبلغ مسدود شده',
            'releasable' => 'آماده تسویه',
            'settled' => 'تسویه شده با میزبان',
        ];
    }
}
