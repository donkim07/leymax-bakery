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
        Schema::create('manufacturing_wastes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacturing_process_id');
            $table->foreign('manufacturing_process_id', 'mfg_waste_process_fk')
                ->references('id')
                ->on('manufacturing_processes')
                ->onDelete('cascade');
            
            $table->unsignedBigInteger('manufacturing_process_ingredient_id')->nullable();
            $table->foreign('manufacturing_process_ingredient_id', 'mfg_waste_ingredient_fk')
                ->references('id')
                ->on('manufacturing_process_ingredients')
                ->onDelete('set null');
                
            $table->foreignId('ingredient_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('assembled_item_id_ref')->nullable()->constrained('assembled_items');
            $table->string('source_name');
            $table->string('source_type');
            $table->decimal('used_amount', 15, 4);
            $table->decimal('waste_amount', 15, 4);
            $table->string('unit');
            $table->decimal('waste_percentage', 8, 2)->default(0);
            $table->text('reason')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_wastes');
    }
}; 