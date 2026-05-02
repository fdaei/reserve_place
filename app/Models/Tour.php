<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'province_id',
        'city_id',
        'user_id',
        'address',
        'tour_type',
        'residence_type',
        'tour_duration',
        'min_people',
        'max_people',
        'description',
        'tour_time_frame',
        'open_tour_time',
        'expire_date',
        'amount',
        'image',
        'status',
        'vip',
        'point',
        'calls',
        'view',
    ];

    public static function getTourType($index = null)
    {
        $list = config('entity-types.tour_types', []);
        if ($index != null) {
            return $list[$index];
        }

        return $list;
    }

    public static function getResidenceType($index = null)
    {
        $list = config('entity-types.tour_residence_types', []);
        if ($index != null) {
            return $list[$index];
        }

        return $list;
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'tour_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'tour_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
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

    public function scopeSearch(Builder $query, $searchText)
    {
        return $query->where(function (Builder $builder) use ($searchText) {
            $builder
                ->where('title', 'like', '%'.$searchText.'%')
                ->orWhere('address', 'like', '%'.$searchText.'%')
                ->orWhere('id', $searchText);
        });
    }
}
