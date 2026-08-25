<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('enquiries')
            ->whereNotIn('status', ['new', 'contacted', 'booked', 'closed'])
            ->update(['status' => 'new']);

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('status')->default('new')->change();
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
