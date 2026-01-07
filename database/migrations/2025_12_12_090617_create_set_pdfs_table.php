<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('set_pdfs', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('url_path');
            $table->unsignedBigInteger('set_id');
            $table->foreign('set_id')->references('id')->on('question_sets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('set_pdfs');
    }
};
