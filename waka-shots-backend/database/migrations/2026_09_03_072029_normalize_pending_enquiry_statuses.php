<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The public enquiry form kept submitting "pending" long after statuses
     * were normalised to new/contacted/booked/closed, so those enquiries
     * matched none of the admin filters and were missed by the dashboard's
     * "New Enquiries" count. The form no longer sets a status at all; this
     * catches the rows it already created.
     */
    public function up(): void
    {
        DB::table('enquiries')
            ->whereNotIn('status', ['new', 'contacted', 'booked', 'closed'])
            ->update(['status' => 'new']);
    }

    public function down(): void
    {
        // No safe reversal — the original per-row values are not recoverable,
        // and "pending" was never a status the rest of the app understood.
    }
};
