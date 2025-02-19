<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('te_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('te_img_id'); // No unique constraint here
            $table->string('te_img_src');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');

            // Optional: Add a composite unique constraint to allow multiple exams but unique per exam
            $table->unique(['exam_id', 'te_img_id']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('te_images');
    }
};
