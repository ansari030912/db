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
        Schema::create('recently_updated', function (Blueprint $table) {
            $table->id();
            $table->string('exam_code', 50); // Change to string to support values like "4A0-104"
            $table->string('exam_title');
            $table->string('exam_perma')->unique();
            $table->integer('exam_questions')->nullable();
            $table->date('exam_update_date');
            $table->string('exam_vendor_title');
            $table->string('exam_vendor_perma');
            $table->string('exam_vendor_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recently_updated');
    }
};
