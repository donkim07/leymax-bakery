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
        Schema::table('assembly_categories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('business_id')->constrained()->onDelete('cascade');
        });

        Schema::table('assembly_groups', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('business_id')->constrained()->onDelete('cascade');
        });

        Schema::table('assembly_sizes', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('business_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assembly_categories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('assembly_groups', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('assembly_sizes', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
}; 