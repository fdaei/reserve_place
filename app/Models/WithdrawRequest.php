<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'reviewed_by',
        'amount',
        'available_balance_snapshot',
        'iban',
        'card_number',
        'account_owner',
        'status',
        'reviewed_at',
        'paid_at',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'integer',
        'available_balance_snapshot' => 'integer',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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
