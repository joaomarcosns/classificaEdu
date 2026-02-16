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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->date('observation_date');
            $table->enum('category', [
                'comportamento',
                'participacao',
                'cooperacao',
                'responsabilidade',
                'interacao_social',
                'outro',
            ]);
            $table->enum('sentiment', ['positivo', 'neutro', 'preocupante']);
            $table->longText('description');
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index(['student_id', 'observation_date']);
            $table->index('category');
            $table->index('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
