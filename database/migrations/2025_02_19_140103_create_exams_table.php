<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->primary(); // Exam ID from API
            $table->string('exam_code')->unique(); // Exam code
            $table->string('exam_title'); // Exam title
            $table->string('exam_perma')->nullable(); // Exam permalink
            $table->integer('exam_questions')->nullable(); // Number of questions

            // Vendor details (linked to vendors table)
            $table->unsignedBigInteger('vendor_id');
            $table->string('vendor_title'); // Vendor name from API
            $table->string('vendor_perma')->nullable(); // Vendor permalink

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
