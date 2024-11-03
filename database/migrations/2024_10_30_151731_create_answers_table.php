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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assingtest_id')->constrained('assigntests')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('option_id')->nullable()->constrained('questionoptions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
