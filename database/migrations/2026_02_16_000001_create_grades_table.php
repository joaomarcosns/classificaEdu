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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 4, 2);
            $table->date('evaluation_date');
            $table->enum('evaluation_period', ['trimestre_1', 'trimestre_2', 'trimestre_3', 'final']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'evaluation_period']);
            $table->index('student_id');
            $table->index('evaluation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
