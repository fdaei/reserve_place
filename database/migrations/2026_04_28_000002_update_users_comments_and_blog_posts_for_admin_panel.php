<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->after('profile_image')->index();
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (! Schema::hasColumn('comments', 'body')) {
                    $table->text('body')->nullable()->after('point');
                }

                if (! Schema::hasColumn('comments', 'status')) {
                    $table->string('status')->default('approved')->after('body')->index();
                }
            });
        }

        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_posts', 'category')) {
                    $table->string('category')->nullable()->after('body')->index();
                }
            });

            if (Schema::hasColumn('blog_posts', 'blog_category_id') && Schema::hasColumn('blog_posts', 'category')) {
                DB::table('blog_posts')
                    ->whereNull('blog_category_id')
                    ->whereNotNull('category')
                    ->orderBy('id')
                    ->chunkById(100, function ($posts) {
                        foreach ($posts as $post) {
                            $slug = \Illuminate\Support\Str::slug($post->category);
                            $category = DB::table('blog_categories')->where('slug', $slug)->first();

                            if (! $category) {
                                $id = DB::table('blog_categories')->insertGetId([
                                    'name' => $post->category,
                                    'slug' => $slug ?: 'category-'.$post->id,
                                    'status' => true,
                                    'sort_order' => 0,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            } else {
                                $id = $category->id;
                            }

                            DB::table('blog_posts')
                                ->where('id', $post->id)
                                ->update(['blog_category_id' => $id]);
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_seen_at');
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (Schema::hasColumn('comments', 'body')) {
                    $table->dropColumn('body');
                }

                if (Schema::hasColumn('comments', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
