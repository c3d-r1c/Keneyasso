<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\DomainEvent;

/**
 * Événement émis quand un médecin est inscrit dans le système.
 *
 * Les abonnés potentiels :
 * - Module Rendez-vous : le médecin devient disponible pour des créneaux
 * - Module Consultations : prépare son espace de suivi
 * - Service notification : envoie un email/SMS de bienvenue
 *
 * Le nomComplet et la spécialité sont embarqués pour éviter aux abonnés
 * d'aller recharger le Docteur depuis la base de données.
 */
final class DocteurInscrit extends DomainEvent
{
    public function __construct(
        public readonly string $docteurId,
        public readonly string $nomComplet,
        public readonly string $specialite,
    ) {
        parent::__construct();
    }
}
