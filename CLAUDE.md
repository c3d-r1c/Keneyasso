# Application Médicale — Contexte pour Claude Code

## Architecture
- Modulaire : `app/Core/` + `Modules/{Patients,Doctors,...}`
- Chaque module : Domain / Application / Infrastructure / Presentation
- Domain = AUCUNE dépendance Laravel (PHP pur)
- Communication inter-modules : Contrats + Interfaces + Événements UNIQUEMENT
  (jamais Patient::create() depuis un autre module)

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

## Conventions
- Une classe = une responsabilité
- Préférer plusieurs petites classes
- Factories + Builders pour les tests (PatientBuilder::new()->adult()->insured())
- Toujours Mail::fake(), Event::fake(), etc.