<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assembly_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('assembly_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('assembly_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assembled_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->comment('single, paste');
            $table->string('unit')->default('piece');
            $table->decimal('selling_price', 15, 2);
            $table->decimal('other_costs', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2);
            $table->foreignId('assembly_category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assembly_group_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assembly_size_id')->nullable()->constrained()->onDelete('set null');
            $table->string('category')->nullable();
            $table->string('group')->nullable();
            $table->string('size')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assembled_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembled_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assembled_item_id_ref')->nullable()->references('id')->on('assembled_items')->onDelete('set null');
            $table->decimal('quantity', 15, 3);
            $table->string('unit');
            $table->decimal('cost', 15, 2);
            $table->timestamps();
        });

        Schema::create('paste_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembled_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('output_assembled_item_id')->nullable()->references('id')->on('assembled_items')->onDelete('set null');
            $table->decimal('quantity', 15, 3);
            $table->string('unit');
            $table->string('flavor')->nullable();
            $table->decimal('flavor_quantity', 15, 3)->nullable();
            $table->string('flavor_unit')->nullable();
            $table->decimal('flavor_cost', 15, 2)->nullable();
            $table->decimal('waste_quantity', 15, 3)->nullable();
            $table->string('waste_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paste_divisions');
        Schema::dropIfExists('assembled_item_ingredients');
        Schema::dropIfExists('assembled_items');
        Schema::dropIfExists('assembly_sizes');
        Schema::dropIfExists('assembly_groups');
        Schema::dropIfExists('assembly_categories');
    }
}; 