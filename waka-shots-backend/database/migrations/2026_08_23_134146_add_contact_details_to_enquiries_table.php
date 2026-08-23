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
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email')->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->date('preferred_date')->nullable()->after('package_id');
            $table->string('location')->nullable()->after('preferred_date');
            $table->string('budget')->nullable()->after('location');
            $table->text('details')->nullable()->after('budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'email',
                'phone',
                'preferred_date',
                'location',
                'budget',
                'details',
            ]);
        });
    }
};
