<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unlimited_access', function (Blueprint $table) {
            $table->id();
            $table->decimal('pdf_full_price', 8, 2);
            $table->decimal('pdf_price', 8, 2);
            $table->text('pdf_cart');
            $table->decimal('te_full_price', 8, 2);
            $table->decimal('te_price', 8, 2);
            $table->text('te_cart');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlimited_access');
    }
};
