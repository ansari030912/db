<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hot_exams', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['week', 'month']);
            $table->string('vendor_title');
            $table->string('vendor_perma');
            $table->string('exam_code');
            $table->string('exam_title');
            $table->string('exam_perma');
            $table->unsignedBigInteger('exam_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hot_exams');
    }
};
