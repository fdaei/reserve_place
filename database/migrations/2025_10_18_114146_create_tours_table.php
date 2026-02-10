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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->foreignId("province_id")->constrained()->onDelete("cascade");
            $table->foreignId("city_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("address");
            $table->text("description");

            $table->integer("tour_type")->default(1);
            $table->integer("residence_type")->default(1);
            $table->integer("tour_duration")->default(1);
            $table->integer("min_people")->default(1);
            $table->integer("max_people")->default(1);
            $table->string("tour_time_frame")->default(1);
            $table->string("open_tour_time")->nullable();
            $table->date("expire_date")->nullable();
            $table->integer("amount")->default(1);
            $table->string("image")->default(1);
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
        Schema::dropIfExists('tours');
    }
};
