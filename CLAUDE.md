# Application Médicale — Contexte pour Claude Code

## Philosophie
- Exploiter Laravel au maximum — ne pas réinventer ce qu'il fournit
- Rester agnostique là où ça protège la testabilité : Domain + Application sans Eloquent direct
- Modules **indépendants** : chaque module se charge lui-même (routes, migrations, bindings)

## Structure des modules
Chaque module vit dans `Modules/{Nom}/` à la racine et s'auto-enregistre via son ServiceProvider.

```
Modules/
└── Patients/
    ├── Providers/
    │   └── PatientsServiceProvider.php   ← charge routes, migrations, bindings
    ├── Domain/
    │   ├── Patient.php                   ← AggregateRoot
    │   ├── PatientId.php, Nom.php, ...   ← ValueObjects
    │   ├── PatientRepository.php         ← interface (contrat)
    │   ├── PatientInscrit.php            ← DomainEvent
    │   └── PatientIntrouvable.php        ← DomainException
    ├── Application/
    │   ├── InscrirePatientCommand.php    ← DTO scalaire
    │   └── InscrirePatientHandler.php    ← orchestrateur
    ├── Infrastructure/
    │   ├── Persistence/
    │   │   ├── PatientModel.php          ← Eloquent model (privé au module)
    │   │   └── EloquentPatientRepository.php ← implémente PatientRepository
    │   └── database/migrations/
    └── Presentation/
        ├── Http/
        │   ├── Controllers/PatientController.php
        │   └── Requests/InscrirePatientRequest.php
        ├── Livewire/                     ← composants Livewire du module
        └── routes/web.php               ← routes propres au module
```

## Ce qu'on utilise dans chaque couche

| Couche         | Laravel autorisé                                        | À éviter               |
|----------------|---------------------------------------------------------|------------------------|
| Domain         | `illuminate/contracts` (interfaces seulement)           | Eloquent, DB::, facades|
| Application    | Events, Jobs, Mail (abstractions)                       | Eloquent direct        |
| Infrastructure | Eloquent, migrations, DB::, bindings                    | Logique métier         |
| Presentation   | Controllers, Livewire, FormRequest, Route, Resource     | Logique métier         |

**Règle pratique :** Domain + Application doivent être testables sans `RefreshDatabase`.

## Communication inter-modules
Via Contrats + Interfaces + Événements Laravel UNIQUEMENT.
Jamais `Patient::find()` ou `PatientModel::` depuis un autre module.

## Tests
```
tests/
├── Unit/Modules/Patients/
│   ├── Domain/      ← sans RefreshDatabase
│   └── Application/ ← sans RefreshDatabase
└── Feature/Modules/Patients/
    └── Http/        ← avec RefreshDatabase
```

## Workflow OBLIGATOIRE : TDD strict
1. Écris d'abord le test (il doit échouer)
2. Montre-moi le test qui échoue AVANT d'écrire le code
3. Code le minimum pour passer
4. Refactor avec tous les tests verts
Ne JAMAIS écrire le code de production avant le test.

## Ce qu'on teste / ne teste pas
- Tester : règles métier, invariants, événements domaine
- NE PAS tester Laravel (belongsTo, hasMany, validation native, routes, middleware)

## Outils qualité — lancer avant toute fin de tâche
- vendor/bin/pest
- vendor/bin/phpstan analyse (niveau max + Larastan)
- vendor/bin/pint
- vendor/bin/rector --dry-run

## Langage métier (à utiliser partout)
Patient, Médecin, Consultation, Rendez-vous, Prescription, Ordonnance,
Facture, Laboratoire, Pharmacie

## Documentation — OBLIGATOIRE sur tout code écrit
Documenter systématiquement :
- **Classes** : PHPDoc expliquant le rôle, le pourquoi, et un exemple d'usage concret
- **Méthodes non triviales** : une ligne sur l'intention (pas sur ce que fait le code)
- **Fichiers de test** : en-tête décrivant la fixture et son équivalent réel dans les modules,
  sections `// ───` pour regrouper les cas, commentaire dans chaque test expliquant
  quelle règle métier il protège
- Ne pas documenter ce que le nom de la méthode dit déjà — documenter le POURQUOI

## Conventions
- Une classe = une responsabilité
- Préférer plusieurs petites classes
- Factories + Builders pour les tests (PatientBuilder::new()->adult()->insured())
- Toujours Mail::fake(), Event::fake(), etc.