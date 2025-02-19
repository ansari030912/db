<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('exam_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('cert_id');
            $table->unsignedBigInteger('vendor_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('cert_id')->references('cert_id')->on('certificates')->onDelete('cascade');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['exam_id', 'cert_id', 'vendor_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_certificates');
    }
};
