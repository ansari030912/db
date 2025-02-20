<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id'); // Foreign key to training_course_vendors
            $table->string('title');
            $table->string('perma')->unique();
            $table->string('image');
            $table->integer('videos');
            $table->bigInteger('duration_milliseconds');
            $table->string('duration');
            $table->unsignedBigInteger('exam_id'); // Foreign key to exams
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('vendor_id')->references('id')->on('training_course_vendors')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_courses');
    }
};
