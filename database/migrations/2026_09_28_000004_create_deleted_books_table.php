<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_book_id');
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->integer('published_year');
            $table->decimal('price', 8, 2);
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_books');
    }
};
