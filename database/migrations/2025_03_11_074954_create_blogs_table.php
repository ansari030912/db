<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            // Use blog_perma as the primary key
            $table->string('blog_perma')->primary();
            $table->string('blog_title');
            $table->text('blog_summary');
            // $table->text('blog_content')->nullable();
            $table->string('blog_image');
            $table->string('blog_vendors'); // Optionally change to integer if needed
            $table->integer('blog_views');
            $table->timestamp('blog_publish_date')->nullable();
            $table->timestamp('blog_update_date')->nullable();
            $table->integer('blog_id'); // blog id from API
            $table->json('blog_vendors_list')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
