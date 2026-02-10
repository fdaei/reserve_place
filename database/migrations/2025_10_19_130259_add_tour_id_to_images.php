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
        if (!Schema::hasColumn('images', 'tour_id')) {
            Schema::table('images', function (Blueprint $table) {
                $table->foreignId("tour_id")->nullable()->constrained()->onDelete("cascade");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('images', 'tour_id')) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tour_id');
            });
        }
    }
};
