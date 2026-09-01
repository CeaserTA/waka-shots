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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('tiktok_url')->nullable()->after('facebook_url');

            $table->string('home_hero_image')->nullable();
            $table->string('home_partners_image')->nullable();
            $table->text('footer_about_text')->nullable();

            $table->string('portfolio_hero_image')->nullable();
            $table->string('portfolio_hero_eyebrow')->nullable();
            $table->string('portfolio_hero_heading')->nullable();

            $table->string('contact_image')->nullable();
            $table->string('contact_tagline')->nullable();

            $table->string('photographer_image')->nullable();
            $table->string('photographer_heading')->nullable();
            $table->text('photographer_bio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tiktok_url',
                'home_hero_image',
                'home_partners_image',
                'footer_about_text',
                'portfolio_hero_image',
                'portfolio_hero_eyebrow',
                'portfolio_hero_heading',
                'contact_image',
                'contact_tagline',
                'photographer_image',
                'photographer_heading',
                'photographer_bio',
            ]);
        });
    }
};
