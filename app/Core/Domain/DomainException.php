<?php

declare(strict_types=1);

namespace App\Core\Domain;

/**
 * Exception métier du domaine.
 *
 * Distingue une erreur métier ("patient introuvable", "rendez-vous déjà pris")
 * d'une erreur technique (PDOException, TimeoutException…).
 *
 * La couche Presentation attrape DomainException pour renvoyer une réponse
 * HTTP adaptée (404, 422…) sans connaître les détails internes du module.
 *
 * Chaque module définit ses propres exceptions avec un message explicite :
 *   class PatientIntrouvable extends DomainException {
 *       public static function avecId(PatientId $id): self {
 *           return new self("Patient introuvable : {$id}");
 *       }
 *   }
 */
abstract class DomainException extends \RuntimeException {}
