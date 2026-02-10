<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodStore extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "province_id",
        "city_id",
        "user_id",
        "address",
        "store_type",
        "food_type",
        "open_time",
        "close_time",
        "close_time",
        "lat",
        "lng",
        "image",
        "status",
        "vip",
        "point",
        "calls",
        "view",
    ];




    public static function getStoreType($index=null){
        $list=[
            "1"=>"کافه",
            "2"=>"رستوران",
            "3"=>"فست فود",
            "4"=>"کافی شاپ",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    public static function getFoodType($index=null){
        $list=[
            "1"=>"ایرانی",
            "2"=>"آمریکایی",
            "3"=>"ایتالیایی",
            "4"=>"آسیایی",
            "5"=>"دریایی",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    function images(){
        return $this->hasMany(Images::class,"store_id","id");
    }
    function comments(){
        return $this->hasMany(Comment::class,"store_id","id");
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
    function optionValues(){
        return $this->hasMany(OptionValue::class,"foodstore_id","id");
    }
}
