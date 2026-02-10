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
        Schema::create('residences', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->foreignId("province_id")->constrained()->onDelete("cascade");
            $table->foreignId("city_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");

            $table->integer("residence_type")->default(1);
            $table->integer("area_type")->default(1);
            $table->integer("room_number")->default(0);
            $table->integer("area")->default(0);
            $table->integer("people_number")->default(1);
            $table->integer("amount")->default(1);
            $table->integer("last_week_amount")->default(0);
            $table->string("address")->default("");
            $table->string("image")->default("");
            $table->string("lat")->nullable();
            $table->string("lng")->nullable();
            $table->boolean("status")->default(true);
            $table->boolean("vip")->default(false);
            $table->float("point")->default(0);
            $table->integer("calls")->default(0);
            $table->integer("view")->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};
