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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('customer_tds_it', 15, 2)->default(0)->after('payment_received');
            $table->decimal('customer_tds_gst', 15, 2)->default(0)->after('customer_tds_it');
            $table->decimal('customer_ld', 15, 2)->default(0)->after('customer_tds_gst');
            $table->decimal('customer_any_other', 15, 2)->default(0)->after('customer_ld');
            $table->text('vendor_payment_note')->nullable()->after('vendor_paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'customer_tds_it',
                'customer_tds_gst',
                'customer_ld',
                'customer_any_other',
                'vendor_payment_note'
            ]);
        });
    }
};
