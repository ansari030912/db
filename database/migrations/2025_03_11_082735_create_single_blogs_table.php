<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSingleBlogsTable extends Migration
{
    public function up()
    {
        Schema::create('single_blogs', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            $table->string('blog_perma'); // Foreign key column
            $table->string('blog_title');
            $table->text('blog_summary');
            $table->text('blog_content')->nullable();
            $table->string('blog_image');
            $table->string('blog_vendors');
            $table->integer('blog_views');
            $table->timestamp('blog_publish_date')->nullable();
            $table->timestamp('blog_update_date')->nullable();
            $table->integer('blog_id');
            $table->json('blog_vendors_list')->nullable();
            $table->timestamps();

            // Set blog_perma as a foreign key referencing blogs table
            $table->foreign('blog_perma')
                  ->references('blog_perma')->on('blogs')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('single_blogs');
    }
}
