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
        Schema::table('assembled_items', function (Blueprint $table) {
            // Check if company_id column doesn't exist
            if (!Schema::hasColumn('assembled_items', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('business_id');
            }
            
            // Check if assembly_category_id column doesn't exist
            if (!Schema::hasColumn('assembled_items', 'assembly_category_id')) {
                $table->foreignId('assembly_category_id')->nullable()->after('total_cost')
                    ->constrained()->onDelete('set null');
            }
            
            // Check if assembly_group_id column doesn't exist
            if (!Schema::hasColumn('assembled_items', 'assembly_group_id')) {
                $table->foreignId('assembly_group_id')->nullable()->after('assembly_category_id')
                    ->constrained()->onDelete('set null');
            }
            
            // Check if assembly_size_id column doesn't exist
            if (!Schema::hasColumn('assembled_items', 'assembly_size_id')) {
                $table->foreignId('assembly_size_id')->nullable()->after('assembly_group_id')
                    ->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assembled_items', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('assembled_items', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            
            if (Schema::hasColumn('assembled_items', 'assembly_category_id')) {
                $table->dropForeign(['assembly_category_id']);
                $table->dropColumn('assembly_category_id');
            }
            
            if (Schema::hasColumn('assembled_items', 'assembly_group_id')) {
                $table->dropForeign(['assembly_group_id']);
                $table->dropColumn('assembly_group_id');
            }
            
            if (Schema::hasColumn('assembled_items', 'assembly_size_id')) {
                $table->dropForeign(['assembly_size_id']);
                $table->dropColumn('assembly_size_id');
            }
        });
    }
};
