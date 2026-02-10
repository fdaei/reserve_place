<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    use HasFactory;
    protected $fillable=[
        "title",
        "province_id",
        "city_id",
        "user_id",
        "residence_type",
        "area_type",
        "room_number",
        "area",
        "people_number",
        "amount",
        "last_week_amount",
        "address",
        "image",
        "vip",
        "view",
        "lat",
        "lng",
        "point",
        "calls",
        "status",
    ];
    function images(){
        return $this->hasMany(Images::class,"residence_id","id");
    }
    function comments(){
        return $this->hasMany(Comment::class,"residence_id","id");
    }
    function optionValues(){
        return $this->hasMany(OptionValue::class,"residence_id","id");
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
        return $query
            ->orWhere('title', 'like', '%' . $searchText . '%')
            ->orWhere('id',   $searchText );
    }
    public static function getResidenceType($index=null){
        $list=[
            "1"=>"ویلا",
            "2"=>"آپارتمان",
            "3"=>"سوئیت",
            "4"=>"کلبه",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }
    public static function getAreaType($index=null){
        $list=[
            "1"=>"جنگلی",
            "2"=>"ساحلی",
            "3"=>"شهری",
            "4"=>"کوهستانی",
            "5"=>"کویری",
            "6"=>"روستایی",
        ];
        if ($index!=null){
            return $list[$index];
        }
        return $list;
    }

    public static function convertNumberToString($num){
        if ($num=="1")return "یک";
        if ($num=="2")return "دو";
        if ($num=="3")return "سه";
        if ($num=="4")return "چهار";
        if ($num=="5")return "پنج";
        if ($num=="6")return "شش";
        if ($num=="7")return "هفت";
        if ($num=="8")return "هشت";
        if ($num=="9")return "نه";
        if ($num=="10")return "ده";
        if ($num=="11")return "یازده";
    }


}
