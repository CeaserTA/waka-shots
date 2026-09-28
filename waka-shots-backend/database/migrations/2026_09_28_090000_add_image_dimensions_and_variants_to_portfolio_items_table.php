<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Width/height let the page reserve each photo's space before it loads,
     * and variant_widths lists the resized WebP copies that exist for it.
     * All three stay null until the image has been processed, and the page
     * falls back to the plain master image while they are.
     */
    public function up(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('image_path');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->json('variant_widths')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'variant_widths']);
        });
    }
};
