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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->longText("value")->nullable();
            $table->string("type")->nullable();
            $table->timestamps();
        });
        \App\Models\Config::create(["title"=>"website-icon","value"=>"icon.png"]);
        \App\Models\Config::create(["title"=>"website-title","value"=>""]);
        \App\Models\Config::create(["title"=>"website-description","value"=>""]);
        \App\Models\Config::create(["title"=>"website-words","value"=>""]);
        \App\Models\Config::create(["title"=>"website-titleEn","value"=>""]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
