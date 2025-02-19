<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('single_exam', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id(); // Primary key (unsignedBigInteger)
            $table->unsignedBigInteger('exam_id')->unique(); // Foreign key to exams
            $table->string('exam_code')->nullable();
            $table->string('exam_title')->nullable();
            $table->string('exam_perma')->unique();
            $table->integer('exam_questions')->nullable();
            $table->dateTime('exam_update_date')->nullable();
            $table->string('exam_pdf')->nullable();
            $table->string('exam_te')->nullable();
            $table->string('exam_sg')->nullable();

            // Vendor details (linked to vendors table)
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_title')->nullable();
            $table->string('vendor_perma')->nullable();

            $table->longText('exam_article')->nullable();
            $table->decimal('exam_pdf_price', 8, 2)->nullable();
            $table->decimal('exam_ete_price', 8, 2)->nullable();
            $table->decimal('exam_sg_price', 8, 2)->nullable();
            $table->decimal('exam_sc_price', 8, 2)->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->boolean('index_tag')->default(false);
            $table->boolean('exam_preorder')->default(false);
            $table->integer('exam_last_week_passed')->nullable();
            $table->decimal('exam_last_week_average_score', 5, 2)->nullable();
            $table->decimal('exam_last_week_word_to_word', 5, 2)->nullable();

            // JSON fields
            $table->json('exam_certs')->nullable();
            $table->json('exam_training_course')->nullable();
            $table->json('exam_redirect')->nullable();
            $table->json('exam_alternate')->nullable();

            $table->boolean('exam_retired')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_products');
    }
};
