<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumnIfMissing('booking_requests', 'request_number', fn (Blueprint $table) => $table->string('request_number')->nullable()->after('id')->index());
        $this->addColumnIfMissing('booking_requests', 'commission_amount', fn (Blueprint $table) => $table->unsignedBigInteger('commission_amount')->default(0)->after('total_amount'));
        $this->addColumnIfMissing('booking_requests', 'host_share_amount', fn (Blueprint $table) => $table->unsignedBigInteger('host_share_amount')->default(0)->after('commission_amount'));
        $this->addColumnIfMissing('booking_requests', 'host_approval_status', fn (Blueprint $table) => $table->string('host_approval_status')->default('pending')->after('status')->index());
        $this->addColumnIfMissing('booking_requests', 'payment_status', fn (Blueprint $table) => $table->string('payment_status')->default('unpaid')->after('host_approval_status')->index());
        $this->addColumnIfMissing('booking_requests', 'stay_status', fn (Blueprint $table) => $table->string('stay_status')->default('not_started')->after('payment_status')->index());
        $this->addColumnIfMissing('booking_requests', 'settlement_status', fn (Blueprint $table) => $table->string('settlement_status')->default('pending')->after('stay_status')->index());
        $this->addColumnIfMissing('booking_requests', 'rejected_reason', fn (Blueprint $table) => $table->text('rejected_reason')->nullable()->after('notes'));

        $this->addColumnIfMissing('bookings', 'host_share_amount', fn (Blueprint $table) => $table->unsignedBigInteger('host_share_amount')->default(0)->after('commission_amount'));
        $this->addColumnIfMissing('bookings', 'settlement_status', fn (Blueprint $table) => $table->string('settlement_status')->default('pending')->after('payment_status')->index());
        $this->addColumnIfMissing('bookings', 'released_at', fn (Blueprint $table) => $table->timestamp('released_at')->nullable()->after('paid_at'));
        $this->addColumnIfMissing('bookings', 'settled_at', fn (Blueprint $table) => $table->timestamp('settled_at')->nullable()->after('released_at'));

        $this->addColumnIfMissing('host_wallet_transactions', 'available_at', fn (Blueprint $table) => $table->timestamp('available_at')->nullable()->after('reference_number'));
        $this->addColumnIfMissing('host_wallet_transactions', 'meta', fn (Blueprint $table) => $table->json('meta')->nullable()->after('description'));

        $this->addColumnIfMissing('withdraw_requests', 'available_balance_snapshot', fn (Blueprint $table) => $table->unsignedBigInteger('available_balance_snapshot')->default(0)->after('amount'));
        $this->addColumnIfMissing('withdraw_requests', 'account_owner', fn (Blueprint $table) => $table->string('account_owner')->nullable()->after('card_number'));
        $this->addColumnIfMissing('withdraw_requests', 'receipt_path', fn (Blueprint $table) => $table->string('receipt_path')->nullable()->after('notes'));
        $this->addColumnIfMissing('withdraw_requests', 'paid_at', fn (Blueprint $table) => $table->timestamp('paid_at')->nullable()->after('reviewed_at'));

        $this->addColumnIfMissing('commissions', 'host_share_amount', fn (Blueprint $table) => $table->unsignedBigInteger('host_share_amount')->default(0)->after('amount'));

        $this->addColumnIfMissing('provinces', 'banner_image', fn (Blueprint $table) => $table->string('banner_image')->nullable()->after('name'));
        $this->addColumnIfMissing('provinces', 'sort_order', fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0)->after('is_use'));
        $this->addColumnIfMissing('cities', 'banner_image', fn (Blueprint $table) => $table->string('banner_image')->nullable()->after('name'));
        $this->addColumnIfMissing('cities', 'sort_order', fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0)->after('is_use'));

        if (! Schema::hasTable('popular_cities')) {
            Schema::create('popular_cities', function (Blueprint $table) {
                $table->id();
                $table->constrained('cities')->cascadeOnDelete();
                $table->string('image_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('footer_links')) {
            Schema::create('footer_links', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('url');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('settlements')) {
            Schema::create('settlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->index();
                $table->foreignId('withdraw_request_id')->nullable()->constrained('withdraw_requests')->nullOnDelete();
                $table->unsignedBigInteger('amount')->default(0);
                $table->string('card_number')->nullable();
                $table->string('iban')->nullable();
                $table->string('account_owner')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('status')->default('pending')->index();
                $table->string('receipt_path')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('popular_cities');
    }

    private function addColumnIfMissing(string $table, string $column, callable $callback): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($callback) {
            $callback($table);
        });
    }
};
