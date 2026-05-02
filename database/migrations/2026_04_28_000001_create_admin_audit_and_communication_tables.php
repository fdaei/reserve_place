<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('body');
                $table->string('category')->nullable()->index();
                $table->string('featured_image')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->timestamp('published_at')->nullable();
                $table->unsignedInteger('views')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('blog_posts', 'blog_category_id')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->foreignId('blog_category_id')->nullable()->after('user_id')->constrained('blog_categories')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('info')->index();
                $table->string('audience')->default('all')->index();
                $table->string('status')->default('draft')->index();
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sms_templates')) {
            Schema::create('sms_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by')->nullable()->index();
                $table->string('title');
                $table->text('body');
                $table->string('type')->default('general')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sms_logs')) {
            Schema::create('sms_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('phone', 20);
                $table->text('message');
                $table->string('provider')->nullable()->index();
                $table->string('status')->default('draft')->index();
                $table->text('response')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('action')->index();
                $table->string('model_type')->nullable()->index();
                $table->unsignedBigInteger('model_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 1000)->nullable();
                $table->text('description')->nullable();
                $table->json('properties')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('security_events')) {
            Schema::create('security_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('event')->index();
                $table->string('level')->default('info')->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 1000)->nullable();
                $table->json('details')->nullable();
                $table->timestamps();
            });
        }

        $this->ensureIndexes();
        $this->ensureInternalForeignKeys();
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('notifications');

        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'blog_category_id')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropConstrainedForeignId('blog_category_id');
            });
        }

        Schema::dropIfExists('blog_categories');
    }

    private function ensureIndexes(): void
    {
        $this->indexIfMissing('blog_posts', 'user_id');
        $this->indexIfMissing('blog_posts', 'blog_category_id');
        $this->indexIfMissing('blog_posts', 'category');
        $this->indexIfMissing('blog_posts', 'status');

        $this->indexIfMissing('admin_notifications', 'created_by');
        $this->indexIfMissing('admin_notifications', 'user_id');
        $this->indexIfMissing('admin_notifications', 'type');
        $this->indexIfMissing('admin_notifications', 'audience');
        $this->indexIfMissing('admin_notifications', 'status');

        $this->indexIfMissing('sms_templates', 'created_by');
        $this->indexIfMissing('sms_templates', 'type');
        $this->indexIfMissing('sms_templates', 'status');

        $this->indexIfMissing('sms_logs', 'created_by');
        $this->indexIfMissing('sms_logs', 'template_id');
        $this->indexIfMissing('sms_logs', 'user_id');
        $this->indexIfMissing('sms_logs', 'provider');
        $this->indexIfMissing('sms_logs', 'status');

        $this->indexIfMissing('activity_logs', 'user_id');
        $this->indexIfMissing('activity_logs', 'action');
        $this->indexIfMissing('activity_logs', 'model_type');
        $this->indexIfMissing('activity_logs', 'model_id');

        $this->indexIfMissing('security_events', 'user_id');
        $this->indexIfMissing('security_events', 'event');
        $this->indexIfMissing('security_events', 'level');
    }

    private function ensureInternalForeignKeys(): void
    {
        $this->foreignKeyIfMissing('blog_posts', 'blog_category_id', 'blog_categories');
        $this->foreignKeyIfMissing('sms_logs', 'template_id', 'sms_templates');
    }

    private function indexIfMissing(string $table, string|array $columns): void
    {
        $columns = is_array($columns) ? $columns : [$columns];

        if (! Schema::hasTable($table) || ! $this->hasColumns($table, $columns) || Schema::hasIndex($table, $columns)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns) {
            $table->index($columns);
        });
    }

    private function foreignKeyIfMissing(string $table, string $column, string $foreignTable): void
    {
        if (
            ! Schema::hasTable($table)
            || ! Schema::hasTable($foreignTable)
            || ! Schema::hasColumn($table, $column)
            || $this->hasForeignKey($table, [$column])
        ) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($column, $foreignTable) {
            $table->foreign($column)->references('id')->on($foreignTable)->nullOnDelete();
        });
    }

    private function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function hasForeignKey(string $table, array $columns): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (($foreignKey['columns'] ?? []) === $columns) {
                return true;
            }
        }

        return false;
    }
};
