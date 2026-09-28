<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->integer('published_year');
            $table->decimal('price', 8, 2);
            $table->string('status')->default('Available')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
