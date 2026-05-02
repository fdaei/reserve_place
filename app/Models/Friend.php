<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'country_id',
        'province_id',
        'user_id',
        'travel_type',
        'travel_duration',
        'my_gender',
        'my_age',
        'friend_gender',
        'machine_type',
        'start_date',
        'travel_version',
        'image',
        'status',
        'vip',
        'point',
        'calls',
        'view',
    ];

    public static function getTravelType($index = null)
    {
        $list = config('entity-types.travel_types', []);
        if ($index != null) {
            return $list[$index];
        }

        return $list;
    }

    public static function getMachineType($index = null)
    {
        $list = config('entity-types.machine_types', []);
        if ($index != null) {
            return $list[$index];
        }

        return $list;
    }

    public static function getGrnders($index = null)
    {
        $list = config('entity-types.genders', []);
        if ($index != null) {
            return $list[$index];
        }

        return $list;
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'friend_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'friend_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookingRequests()
    {
        return $this->morphMany(BookingRequest::class, 'bookable');
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function optionValues()
    {
        return $this->hasMany(OptionValue::class, 'friend_id', 'id');
    }

    public function scopeSearch(Builder $query, $searchText)
    {
        return $query->where(function (Builder $builder) use ($searchText) {
            $builder
                ->where('title', 'like', '%'.$searchText.'%')
                ->orWhere('id', $searchText);
        });
    }
}
