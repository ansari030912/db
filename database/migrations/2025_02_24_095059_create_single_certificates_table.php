<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('single_certificates', function (Blueprint $table) {
            $table->unsignedBigInteger('cert_id')->primary();
            $table->boolean('has_multiple_exams');
            $table->string('cert_title')->nullable(); // Allow NULL values
            $table->string('cert_perma')->unique();
            $table->string('cert_full_name');
            $table->string('vendor_title');
            $table->string('vendor_perma');
            $table->boolean('is_disabled')->default(false);
            $table->boolean('index_tag')->default(false);
            $table->string('cert_single_exam')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_certificates');
    }
};
