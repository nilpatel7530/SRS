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
        // 1. Projects Table Enhancements
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_extension')->default(false)->after('project_type');
            $table->text('extension_details')->nullable()->after('is_extension');
        });

        // 2. Project Proposals Table Enhancements
        Schema::table('project_proposals', function (Blueprint $table) {
            $table->string('state')->nullable()->after('name');
            $table->text('description_of_work')->nullable()->after('state');
            $table->string('vendor_name')->nullable()->after('description_of_work');
            $table->date('work_order_date')->nullable()->after('vendor_name');
            $table->string('sent_by')->nullable()->after('work_order_date');
            
            // Modify status column to support new options
            $table->string('status', 50)->default('Open')->change(); 
        });

        // 3. Invoices Table Enhancements (Reconciliation & Payment Tracking)
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('work_description')->nullable()->after('cel_invoice_no');
            $table->date('customer_payment_date')->nullable()->after('payment_received');
            $table->text('customer_payment_note')->nullable()->after('customer_payment_date');
            $table->date('vendor_payment_date')->nullable()->after('customer_payment_note');
            $table->decimal('vendor_paid_amount', 15, 2)->default(0)->after('vendor_payment_date');
            $table->decimal('tds_deduction', 15, 2)->default(0)->after('vendor_paid_amount');
            $table->decimal('gst_tds_deduction', 15, 2)->default(0)->after('tds_deduction');
            $table->decimal('bank_charges', 15, 2)->default(0)->after('gst_tds_deduction');
            $table->decimal('ta_da', 15, 2)->default(0)->after('bank_charges');
        });

        // 4. Documents Table Enhancements
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('proposal_id')->nullable()->after('project_id');
            $table->foreign('proposal_id')->references('id')->on('project_proposals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['is_extension', 'extension_details']);
        });

        Schema::table('project_proposals', function (Blueprint $table) {
            $table->dropColumn(['state', 'description_of_work', 'vendor_name', 'work_order_date', 'sent_by']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'work_description', 'customer_payment_date', 'customer_payment_note',
                'vendor_payment_date', 'vendor_paid_amount', 'tds_deduction',
                'gst_tds_deduction', 'bank_charges', 'ta_da'
            ]);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['proposal_id']);
            $table->dropColumn('proposal_id');
        });
    }
};
