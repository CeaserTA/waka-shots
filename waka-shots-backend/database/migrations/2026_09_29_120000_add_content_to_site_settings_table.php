<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editable page copy (headings, intros, stats, lists) lives in one JSON
     * column keyed by page, so new text can be exposed in the admin without
     * a migration per field. Defaults are in SiteSetting::CONTENT_DEFAULTS.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
