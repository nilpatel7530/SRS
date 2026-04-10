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
        // 1. Projects table updates
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('financial_type', ['capex', 'opex'])->after('department_id')->nullable();
            $table->enum('project_type', ['service', 'supply'])->after('financial_type')->nullable();
        });

        // Migrate old type to new financial_type where possible
        DB::table('projects')->where('type', 'capex')->update(['financial_type' => 'capex']);
        DB::table('projects')->where('type', 'opex')->update(['financial_type' => 'opex']);
        DB::table('projects')->where('type', 'service')->update(['project_type' => 'service']);
        DB::table('projects')->where('type', 'supply')->update(['project_type' => 'supply']);

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // 2. Capex Entries updates
        Schema::table('capex_entries', function (Blueprint $table) {
            $table->renameColumn('entry_date', 'completion_date');
            $table->renameColumn('description', 'remarks');
        });

        // 3. Opex Entries updates
        Schema::table('opex_entries', function (Blueprint $table) {
            $table->string('duration')->nullable();
            $table->renameColumn('description', 'remarks');
            // We'll keep entry_date as the record date, but SRS asks for "Duration"
        });

        // 4. Bank Guarantees updates
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->string('bg_no')->after('project_id')->nullable();
            $table->date('bg_date')->after('bg_no')->nullable();
            // Upgrading type enum requires raw SQL or dropping/recreating in some versions, but let's try modification
            $table->string('type', 50)->change(); // Temporarily make it string to change enum
        });
        
        // Use raw SQL to update enum if needed, or just keep it as string for flexibility
        // For SRS: ABG, PBG, AMC-BG

        // 5. Invoices updates
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('vendor_total', 15, 2)->default(0);
            $table->decimal('vendor_gst', 15, 2)->default(0);
            $table->decimal('vendor_total_with_gst', 15, 2)->default(0);
            $table->decimal('cel_total', 15, 2)->default(0);
            $table->decimal('cel_gst', 15, 2)->default(0);
            $table->decimal('cel_total_with_gst', 15, 2)->default(0);
            $table->decimal('payment_received', 15, 2)->default(0);
            $table->text('remarks')->nullable();
        });

        // 6. Documents updates
        Schema::table('documents', function (Blueprint $table) {
            $table->string('type', 50)->change(); // Change from enum to string for SRS headings flexibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->dropColumn(['financial_type', 'project_type']);
        });

        Schema::table('capex_entries', function (Blueprint $table) {
            $table->renameColumn('completion_date', 'entry_date');
            $table->renameColumn('remarks', 'description');
        });

        Schema::table('opex_entries', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->renameColumn('remarks', 'description');
        });

        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->dropColumn(['bg_no', 'bg_date']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_total', 'vendor_gst', 'vendor_total_with_gst',
                'cel_total', 'cel_gst', 'cel_total_with_gst',
                'payment_received', 'remarks'
            ]);
        });
    }
};
