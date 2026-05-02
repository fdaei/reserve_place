<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Residence extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'province_id',
        'city_id',
        'user_id',
        'residence_type',
        'area_type',
        'room_number',
        'area',
        'people_number',
        'amount',
        'last_week_amount',
        'address',
        'image',
        'vip',
        'view',
        'lat',
        'lng',
        'point',
        'calls',
        'status',
    ];

    public function images()
    {
        return $this->hasMany(Images::class, 'residence_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'residence_id', 'id');
    }

    public function optionValues()
    {
        return $this->hasMany(OptionValue::class, 'residence_id', 'id');
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
        return $query
            ->orWhere('title', 'like', '%'.$searchText.'%')
            ->orWhere('id', $searchText);
    }

    public static function getResidenceType($index = null)
    {
        $list = config('entity-types.residence_types', []);
        if ($index != null) {
            return $list[$index] ?? null;
        }

        return $list;
    }

    public static function getAreaType($index = null)
    {
        $list = config('entity-types.area_types', []);
        if ($index != null) {
            return $list[$index] ?? null;
        }

        return $list;
    }

    public static function convertNumberToString($num)
    {
        if ($num == '1') {
            return 'یک';
        }
        if ($num == '2') {
            return 'دو';
        }
        if ($num == '3') {
            return 'سه';
        }
        if ($num == '4') {
            return 'چهار';
        }
        if ($num == '5') {
            return 'پنج';
        }
        if ($num == '6') {
            return 'شش';
        }
        if ($num == '7') {
            return 'هفت';
        }
        if ($num == '8') {
            return 'هشت';
        }
        if ($num == '9') {
            return 'نه';
        }
        if ($num == '10') {
            return 'ده';
        }
        if ($num == '11') {
            return 'یازده';
        }
    }
}
