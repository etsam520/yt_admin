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
        Schema::create('question_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('question_tbls')->onDelete('cascade');
            $table->string('meta_key');
            $table->string('meta_value');

            $table->unique(['question_id', 'meta_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_metas');
    }
};
