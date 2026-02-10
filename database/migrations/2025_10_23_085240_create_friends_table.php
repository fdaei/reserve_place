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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();

            $table->string("title");
            $table->foreignId("country_id")->constrained()->onDelete("cascade");
            $table->foreignId("province_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");

            $table->foreignId("travel_type");
            $table->string("travel_duration");

            $table->integer("my_gender");
            $table->string("my_age");
            $table->integer("friend_gender");
            $table->integer("machine_type");
            $table->date("start_date")->nullable();
            $table->integer("travel_version");
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
        Schema::dropIfExists('friends');
    }
};
