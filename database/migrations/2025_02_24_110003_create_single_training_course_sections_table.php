<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('single_training_course_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->primary(); // Set as primary key
            $table->unsignedBigInteger('course_id'); // Foreign key reference to single_training_courses
            $table->integer('section_seq');
            $table->string('section_title');
            $table->integer('section_lectures'); // Corrected field name
            $table->bigInteger('section_duration_milliseconds'); // Corrected field name
            $table->string('section_duration');
            $table->timestamps();

            // Foreign key constraint referencing the 'course_id' in 'single_training_courses'
            $table->foreign('course_id')->references('course_id')->on('single_training_courses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_training_course_sections');
    }
};
