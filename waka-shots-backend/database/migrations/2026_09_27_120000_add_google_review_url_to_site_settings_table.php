<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('google_review_url')->nullable()->after('x_url');
        });

        // Seed the studio's review link so it reaches production with the deploy;
        // it stays editable in Studio Settings afterwards.
        DB::table('site_settings')
            ->whereNull('google_review_url')
            ->update(['google_review_url' => 'https://g.page/r/Ca1C7ZPGE70tEBM/review']);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('google_review_url');
        });
    }
};
