<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('certificates_multiple_exams', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('exam_id'); // Foreign key to exams
            $table->unsignedBigInteger('cert_id'); // Foreign key to single_certificates
            $table->string('exam_title');
            $table->string('exam_perma');
            $table->boolean('exam_retired')->default(false);
            $table->integer('exam_questions')->nullable(); // Allow NULL values
            $table->string('exam_vendor_title');
            $table->boolean('exam_disabled')->default(false);
            $table->string('exam_vendor_perma');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('cert_id')->references('cert_id')->on('single_certificates')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade'); // Assuming exams table exists
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates_multiple_exams');
    }
};
