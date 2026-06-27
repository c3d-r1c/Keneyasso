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