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
        Schema::create('manufacturing_process_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacturing_process_id');
            $table->foreign('manufacturing_process_id', 'mfg_process_ingredient_fk')
                ->references('id')
                ->on('manufacturing_processes')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('assembled_item_id_ref')->nullable()->constrained('assembled_items');
            $table->decimal('quantity', 15, 4);
            $table->string('unit');
            $table->decimal('cost', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_process_ingredients');
    }
}; 