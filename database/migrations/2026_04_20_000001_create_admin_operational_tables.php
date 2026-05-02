<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('category')->default('general')->index();
            $table->string('featured_image')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('image_path');
                $table->string('link_url')->nullable();
                $table->string('position')->default('home')->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('booking_requests')) {
            Schema::create('booking_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->nullable()->index();
                $table->foreignId('host_id')->nullable()->index();
                $table->foreignId('assigned_to')->nullable()->index();
                $table->nullableMorphs('bookable');
                $table->string('guest_name');
                $table->string('guest_phone');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->unsignedSmallInteger('guests_count')->default(1);
                $table->unsignedBigInteger('total_amount')->default(0);
                $table->string('status')->default('pending')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_request_id')->nullable()->constrained('booking_requests')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->index();
                $table->foreignId('host_id')->nullable()->index();
                $table->nullableMorphs('bookable');
                $table->string('booking_number')->unique();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->unsignedSmallInteger('guests_count')->default(1);
                $table->unsignedBigInteger('subtotal')->default(0);
                $table->unsignedBigInteger('discount_amount')->default(0);
                $table->unsignedBigInteger('commission_amount')->default(0);
                $table->unsignedBigInteger('total_amount')->default(0);
                $table->string('status')->default('pending')->index();
                $table->string('payment_status')->default('unpaid')->index();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('host_wallet_transactions')) {
            Schema::create('host_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->index();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->string('type')->default('credit')->index();
                $table->bigInteger('amount');
                $table->bigInteger('balance_after')->default(0);
                $table->string('status')->default('posted')->index();
                $table->string('reference_number')->nullable()->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('withdraw_requests')) {
            Schema::create('withdraw_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->index();
                $table->foreignId('reviewed_by')->nullable()->index();
                $table->unsignedBigInteger('amount');
                $table->string('iban')->nullable();
                $table->string('card_number')->nullable();
                $table->string('status')->default('pending')->index();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('commissions')) {
            Schema::create('commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->foreignId('host_id')->nullable()->index();
                $table->decimal('rate', 5, 2)->default(10);
                $table->unsignedBigInteger('amount')->default(0);
                $table->string('status')->default('pending')->index();
                $table->timestamp('settled_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('discount_codes')) {
            Schema::create('discount_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by')->nullable()->index();
                $table->string('code')->unique();
                $table->string('title');
                $table->string('type')->default('percent');
                $table->unsignedInteger('value');
                $table->unsignedBigInteger('max_amount')->nullable();
                $table->unsignedBigInteger('min_order_amount')->default(0);
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('withdraw_requests');
        Schema::dropIfExists('host_wallet_transactions');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_requests');
        Schema::dropIfExists('banners');
    }
};
