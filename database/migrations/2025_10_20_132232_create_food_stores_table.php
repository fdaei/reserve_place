<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_stores', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->foreignId("province_id")->constrained()->onDelete("cascade");
            $table->foreignId("city_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("address");

            $table->integer("store_type");
            $table->integer("food_type");
            $table->time("open_time");
            $table->time("close_time");
            $table->string("lat")->nullable();
            $table->string("lng")->nullable();
            $table->string("image");
            $table->integer("status")->default(1);
            $table->integer("vip")->default(1);
            $table->integer("point")->default(1);
            $table->integer("calls")->default(1);
            $table->integer("view")->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_stores');
    }
};
