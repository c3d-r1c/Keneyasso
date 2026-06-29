<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docteurs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('prenom');
            $table->string('nom_de_famille');
            $table->string('specialite');
            $table->string('numero_ordre')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docteurs');
    }
};
