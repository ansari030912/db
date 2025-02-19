<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->primary();
            $table->string('vendor_title')->nullable();
            $table->string('vendor_perma')->unique();
            $table->string('vendor_img')->nullable();
            $table->integer('vendor_exams')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
