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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type');
            $table->unsignedBigInteger('size')->nullable()->after('category');
            $table->unsignedBigInteger('uploader_id')->nullable()->after('size');
            
            // Add name and path as aliases/fallbacks if we want to avoid renaming controller fields
            // but it's better to just update the controller.
            // For now, let's keep it simple and just add the metadata fields.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['category', 'size', 'uploader_id']);
        });
    }
};
