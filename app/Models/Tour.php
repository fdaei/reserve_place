<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "province_id",
        "city_id",
        "user_id",
        "address",
        "tour_type",
        "residence_type",
        "tour_duration",
        "min_people",
        "max_people",
        "description",
        "tour_time_frame",
        "open_tour_time",
        "expire_date",
        "amount",
        "image",
        "status",
        "vip",
        "point",
        "calls",
        "view",
    ];



    public static function getTourType($index=null){
        $list=[
            "1"=>"کویرگردی",
            "2"=>"آفرودی",
            "3"=>"شهرگردی",
            "4"=>"کوهنوردی",
            "5"=>"طبیعت گردی",
            "6"=>"مزه گردی",
            "7"=>"روستایی",
            "8"=>"تاریخی",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    public static function getResidenceType($index=null){
        $list=[
            "1"=>"کمپ",
            "2"=>"اقامت محلی",
            "3"=>"مهمان سرا",
            "4"=>"هتل 1 ستاره",
            "5"=>"هتل 2 ستاره",
            "6"=>"هتل 3 ستاره",
            "7"=>"هتل 4 ستاره",
            "8"=>"بدون اقامت",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    function images(){
        return $this->hasMany(Images::class,"tour_id","id");
    }
    function comments(){
        return $this->hasMany(Comment::class,"tour_id","id");
    }
    function province(){
        return $this->hasOne(Province::class,"id","province_id");
    }
    function city(){
        return $this->hasOne(City::class,"id","city_id");
    }
    function admin(){
        return $this->hasOne(User::class,"id","user_id");
    }

    public function scopeSearch(Builder $query, $searchText){
        return $query->where(function (Builder $builder) use ($searchText) {
            $builder
                ->where('title', 'like', '%' . $searchText . '%')
                ->orWhere('address', 'like', '%' . $searchText . '%')
                ->orWhere('id', $searchText);
        });
    }
}
