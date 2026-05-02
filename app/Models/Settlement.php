<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'withdraw_request_id',
        'amount',
        'card_number',
        'iban',
        'account_owner',
        'requested_at',
        'paid_at',
        'status',
        'receipt_path',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'requested_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function withdrawRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawRequest::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $builder) use ($search) {
            $builder->where('iban', 'like', '%'.$search.'%')
                ->orWhere('card_number', 'like', '%'.$search.'%')
                ->orWhereHas('host', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
        });
    }
}
