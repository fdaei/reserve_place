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
        $userIdType = Schema::hasTable('users')
            ? Schema::getColumnType('users', 'id')
            : 'bigint';

        Schema::create('role_user', function (Blueprint $table) use ($userIdType) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');

            // Match user_id type with existing users.id to avoid FK type mismatch on legacy schemas.
            if (in_array($userIdType, ['int', 'integer', 'mediumint', 'smallint', 'tinyint'], true)) {
                $table->unsignedInteger('user_id');
            } else {
                $table->unsignedBigInteger('user_id');
            }

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
