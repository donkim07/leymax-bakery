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
        Schema::create('manufacturing_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('assembled_item_id')->constrained();
            $table->string('batch_number')->unique();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->string('status')->default('in_progress');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_processes');
    }
}; 