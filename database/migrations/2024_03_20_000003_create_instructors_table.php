<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('specialization')->nullable();
            $table->string('status')->default('active');
            $table->string('avatar_path')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->json('availability')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add instructor_id to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
        });

        // Add instructor_id to lessons table
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });

        Schema::dropIfExists('instructors');
    }
}; 