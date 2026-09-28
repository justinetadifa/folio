<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->foreignId('borrower_id')->constrained('borrowers', 'borrower_id')->restrictOnDelete();
            $table->foreignId('book_id')->constrained('books', 'book_id')->restrictOnDelete();
            $table->date('rental_date');
            $table->date('return_date')->nullable();
            $table->string('status')->default('Rented');
            $table->index(['book_id', 'status']);
            $table->index(['status', 'rental_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
