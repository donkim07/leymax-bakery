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
            // Add company_id field
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('business_id');
            
            // Add assembly fields
            $table->foreignId('assembly_category_id')->nullable()->after('total_cost')
                ->constrained()->onDelete('set null');
            $table->foreignId('assembly_group_id')->nullable()->after('assembly_category_id')
                ->constrained()->onDelete('set null');
            $table->foreignId('assembly_size_id')->nullable()->after('assembly_group_id')
                ->constrained()->onDelete('set null');
                
            // Keep the old fields for backward compatibility, but make them nullable
            $table->string('category')->nullable()->change();
            $table->string('group')->nullable()->change();
            $table->string('size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assembled_items', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            
            $table->dropForeign(['assembly_category_id']);
            $table->dropColumn('assembly_category_id');
            
            $table->dropForeign(['assembly_group_id']);
            $table->dropColumn('assembly_group_id');
            
            $table->dropForeign(['assembly_size_id']);
            $table->dropColumn('assembly_size_id');
            
            // Restore the old fields to their original state
            $table->string('category')->change();
            $table->string('group')->change();
            $table->string('size')->change();
        });
    }
};
