<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée la table patients.
 *
 * L'id est un UUID stocké en string — cohérent avec PatientId (EntityId).
 * Les colonnes métier reflètent les ValueObjects du Domain :
 * Nom (prenom + nom_de_famille) et DateDeNaissance (date_de_naissance).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('prenom');
            $table->string('nom_de_famille');
            $table->date('date_de_naissance');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
