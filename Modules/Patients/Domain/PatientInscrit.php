<?php

declare(strict_types=1);

namespace Modules\Patients\Domain;

use App\Core\Domain\DomainEvent;

/**
 * Événement émis quand un patient est inscrit dans le système.
 *
 * Les abonnés potentiels :
 * - Module Laboratoire : prépare un dossier d'analyses vide
 * - Module Pharmacie : initialise l'historique des prescriptions
 * - Service notification : envoie un email/SMS de bienvenue
 *
 * Le nomComplet est embarqué pour éviter aux abonnés
 * d'aller recharger le Patient depuis la base de données.
 */
final class PatientInscrit extends DomainEvent
{
    public function __construct(
        public readonly string $patientId,
        public readonly string $nomComplet,
    ) {
        parent::__construct();
    }
}
