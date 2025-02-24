<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('single_training_course_lectures', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('section_id'); // Foreign key reference to single_training_course_sections
            $table->unsignedBigInteger('lecture_id');
            $table->integer('lecture_seq');
            $table->string('lecture_title');
            $table->string('lecture_duration_timespan');
            $table->string('lecture_duration');
            $table->timestamps();

            // Foreign key constraint referencing 'section_id' in 'single_training_course_sections'
            $table->foreign('section_id')->references('section_id')->on('single_training_course_sections')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_training_course_lectures');
    }
};
