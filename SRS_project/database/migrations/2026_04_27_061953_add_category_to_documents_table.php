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
        if (!Schema::hasColumn('documents', 'category')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('category', 20)->default('customer')->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('documents', 'category')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
