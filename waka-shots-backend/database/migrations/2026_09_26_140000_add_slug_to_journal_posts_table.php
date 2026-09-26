<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_posts', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Backfill existing posts from their titles, oldest first, so the earliest
        // post keeps the plain slug and later duplicates get -2, -3, ...
        $taken = [];

        DB::table('journal_posts')->orderBy('id')->get(['id', 'title'])->each(function ($post) use (&$taken): void {
            $base = Str::slug($post->title) ?: 'post';
            $slug = $base;

            for ($suffix = 2; in_array($slug, $taken, true); $suffix++) {
                $slug = "{$base}-{$suffix}";
            }

            $taken[] = $slug;

            DB::table('journal_posts')->where('id', $post->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('journal_posts', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
