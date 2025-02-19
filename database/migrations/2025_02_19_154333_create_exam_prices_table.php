<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('exam_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->integer('type');
            $table->string('title')->nullable();
            $table->string('cart')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('full_price', 10, 2)->default(0);
            $table->integer('off')->default(0);
            $table->timestamps();

            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_prices');
    }
};
