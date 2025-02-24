<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('single_training_courses', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->primary(); // course_id as unsigned big integer and primary
            $table->string('perma')->unique();
            $table->string('title');
            $table->string('image');
            $table->bigInteger('duration_milliseconds');
            $table->string('duration');
            $table->integer('lectures')->nullable();
            $table->unsignedBigInteger('exam_id');
            $table->decimal('price', 8, 2);
            $table->decimal('full_price', 8, 2);
            $table->text('cart');
            $table->timestamps();

            // Foreign key constraint for exam_id
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_training_courses');
    }
};
