<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "country_id",
        "province_id",
        "user_id",
        "travel_type",
        "travel_duration",
        "my_gender",
        "my_age",
        "friend_gender",
        "machine_type",
        "start_date",
        "travel_version",
        "image",
        "status",
        "vip",
        "point",
        "calls",
        "view",
    ];



    public static function getTravelType($index=null){
        $list=[
            "1"=>"تفریحی",
            "2"=>"کاری",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    public static function getMachineType($index=null){
        $list=[
            "1"=>"هوایی",
            "2"=>"زمینی",
            "3"=>"دریایی",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    public static function getGrnders($index=null){
        $list=[
            "1"=>"آقا",
            "2"=>"خانم",
            "3"=>"زوج",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    function images(){
        return $this->hasMany(Images::class,"friend_id","id");
    }
    function comments(){
        return $this->hasMany(Comment::class,"friend_id","id");
    }
    function province(){
        return $this->hasOne(Province::class,"id","province_id");
    }
    function country(){
        return $this->hasOne(Country::class,"id","country_id");
    }
    function admin(){
        return $this->hasOne(User::class,"id","user_id");
    }
    function optionValues(){
        return $this->hasMany(OptionValue::class,"friend_id","id");
    }
}
