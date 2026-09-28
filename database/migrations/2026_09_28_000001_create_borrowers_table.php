<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id('borrower_id');
            $table->string('name');
            $table->string('contact_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
