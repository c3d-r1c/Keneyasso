# Application Médicale — Contexte pour Claude Code

## Philosophie
- **nwidart/laravel-modules** installé — utiliser `php artisan module:make {Nom}` pour créer un module
- Exploiter Laravel au maximum — ne pas réinventer ce qu'il fournit
- Rester agnostique là où ça protège la testabilité : Domain + Application sans Eloquent direct
- Modules **indépendants** : chaque module se charge lui-même via `module.json` + `PatientsServiceProvider`

## Structure standard d'un module (nwidart v13)
```
Modules/{Nom}/
├── app/                              ← tout le code PHP du module
│   ├── Domain/                       ← ValueObjects, AggregateRoot, Events, interface Repository
│   ├── Actions/                      ← actions invocables (une par use case), injectées par Laravel
│   ├── Http/
│   │   ├── Controllers/              ← standard Laravel (mince : délègue à l'Action)
│   │   └── Requests/                 ← FormRequests
│   ├── Models/                       ← Eloquent (privé au module)
│   ├── Providers/
│   │   └── {Nom}ServiceProvider.php  ← charge routes + bindings
│   └── Repositories/                 ← implémentations concrètes
├── database/
│   └── migrations/                   ← auto-découvertes par nwidart
├── routes/
│   └── web.php                       ← routes du module
├── composer.json                     ← PSR-4 : "Modules\{Nom}\" → "app/"
└── module.json                       ← descripteur nwidart (provider, alias…)
```

**Autoloading :** chaque `Modules/{Nom}/composer.json` est fusionné par `wikimedia/composer-merge-plugin`.
**Activation :** `modules_statuses.json` à la racine — `php artisan module:enable {Nom}`.
**Provider :** chargé automatiquement par nwidart via `module.json`, pas dans `bootstrap/providers.php`.

## Ce qu'on utilise dans chaque couche

| Couche         | Laravel autorisé                                                                       | À éviter                        |
|----------------|----------------------------------------------------------------------------------------|---------------------------------|
| Domain         | `illuminate/contracts` (interfaces), `illuminate/support` (Carbon, Str, Collection)   | Eloquent, DB::, facades I/O     |
| Application    | `Illuminate\Contracts\Events\Dispatcher`, Jobs, Mail (contrats uniquement)             | Eloquent direct                 |
| Infrastructure | Eloquent, migrations, DB::, bindings                                                   | Logique métier                  |
| Presentation   | Controllers, Livewire, FormRequest, Route, Resource                                    | Logique métier                  |

**Règle réelle :** Domain + Application doivent être testables sans `RefreshDatabase`.
Ce qui est interdit n'est pas "tout Laravel" mais **l'infrastructure** : pas d'Eloquent direct, pas de `DB::`,
pas de facades qui touchent la BDD, le filesystem ou HTTP.
`Carbon`, `Str`, `Collection` sont des bibliothèques pures — zéro I/O, parfaitement testables en isolation.

**Actions invocables :** une Action = un use case. Elle injecte le Repository et le Dispatcher,
construit les ValueObjects, persiste, puis dispatche les DomainEvents APRÈS `save()`.
Le container Laravel résout les dépendances automatiquement dans le Controller :
`public function store(MyRequest $request, MonAction $action): RedirectResponse`

## Communication inter-modules
Via Contrats + Interfaces + Événements Laravel UNIQUEMENT.
Jamais `Patient::find()` ou `PatientModel::` depuis un autre module.

## Tests
```
tests/
├── Unit/Modules/Patients/
│   ├── Domain/      ← sans RefreshDatabase
│   └── Actions/     ← sans RefreshDatabase (doubles InMemory + DispatcherSpy)
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