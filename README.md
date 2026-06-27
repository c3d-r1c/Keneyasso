# 🏥 Feuille de Route — Développement d'une Application Médicale (Laravel)

> Objectif : Construire une plateforme médicale modulaire, maintenable et testable, en appliquant le TDD dès le début afin que les évolutions futures restent simples et fiables.

---

# 🎯 Vision

## Objectifs

* [ ] Définir le MVP
* [ ] Identifier les utilisateurs
* [ ] Définir les rôles
* [ ] Décrire les workflows principaux
* [ ] Établir un glossaire métier

### Exemple de vocabulaire métier

```
Patient

Médecin

Consultation

Rendez-vous

Prescription

Ordonnance

Facture

Laboratoire

Pharmacie
```

> 💡 **Tip**
>
> Le langage métier doit être utilisé partout : code, documentation et tests.

---

# 🏗️ Architecture

## Stack technique

* Laravel
* Livewire 3
* PostgreSQL
* PestPHP
* PHPStan
* Laravel Pint
* Rector
* GitHub Actions

---

# 📁 Structure générale

```
app/

    Core/

        Contracts/

        Events/

        Exceptions/

        Authentication/

        Authorization/

        Shared/

Modules/

    Patients/

    Doctors/

    Appointments/

    Consultations/

    Billing/

    Pharmacy/

    Laboratory/
```

---

# 📦 Structure d'un module

```
Patients/

    Domain/

    Application/

    Infrastructure/

    Presentation/

    Database/

    Tests/

    Resources/

    Providers/

    module.json
```

---

# 📚 Organisation interne

## Domain

Contient uniquement le métier.

```
Patient

PatientRepository

PatientNumber

Gender

Age
```

Aucune dépendance vers Laravel.

---

## Application

Cas d'utilisation.

```
RegisterPatient

UpdatePatient

ArchivePatient
```

Chaque classe représente une action métier.

---

## Infrastructure

Connexion avec Laravel.

```
EloquentPatientRepository

PatientModel

Observers
```

---

## Presentation

```
Controllers

Requests

Resources

React

Livewire
```

---

# 🧪 Mettre le TDD au cœur du développement

## Cycle à suivre

```
Écrire un test

↓

Le test échoue

↓

Coder le minimum

↓

Le test passe

↓

Refactoriser

↓

Tous les tests restent verts
```

---

# Exemple

Créer un patient.

```
RegisterPatientTest

↓

Créer RegisterPatient

↓

Créer Patient

↓

Créer Repository

↓

Le test passe

↓

Refactor
```

---

# ✅ Pyramide des tests

```
            UI

         Feature

     Integration

Unit Unit Unit Unit
```

Objectif :

* 70 % Unit
* 20 % Feature
* 10 % UI

---

# 🛠️ Outils de qualité

Installer dès le premier jour.

* [ ] Pest
* [ ] PHPStan
* [ ] Larastan
* [ ] Pint
* [ ] Rector
* [ ] Infection (Mutation Testing)

---

# 📋 Pipeline GitHub

À chaque Push :

```
Pint

↓

PHPStan

↓

Tests

↓

Coverage

↓

Build
```

Aucun merge si une étape échoue.

---

# 🧱 Développement des modules

Commencer uniquement par :

* [ ] Patients

Puis :

* [ ] Doctors
* [ ] Appointments
* [ ] Consultations

Ne jamais développer plusieurs gros modules en parallèle.

---

# 🧪 Ce qu'il faut tester

## Patient

* [ ] Nom obligatoire
* [ ] Date de naissance valide
* [ ] Sexe valide
* [ ] Numéro unique
* [ ] Sauvegarde
* [ ] Événement PatientCreated

---

# ❌ Ce qu'il ne faut PAS tester

Ne pas tester Laravel.

Exemples :

* belongsTo()
* hasMany()
* Route::get()
* Validation Laravel
* Middleware Laravel

Tester uniquement ton métier.

---

# 🏭 Factories

Créer une Factory dès qu'une entité apparaît.

```
PatientFactory

DoctorFactory

AppointmentFactory

PrescriptionFactory
```

---

# 🏗️ Builders

Pour améliorer la lisibilité des tests.

Exemple :

```
PatientBuilder::new()

    ->adult()

    ->male()

    ->insured();
```

Les tests deviennent très expressifs.

---

# 🎭 Fake partout

Toujours utiliser :

```
Mail::fake()

Notification::fake()

Storage::fake()

Queue::fake()

Http::fake()

Event::fake()
```

Les tests deviennent rapides.

---

# 📡 Communication entre modules

Ne jamais faire :

```
Consultation

↓

Patient::create()
```

Toujours passer par :

* Contrats
* Interfaces
* Événements

---

# 📚 Documentation

Chaque module possède son README.

Contenu conseillé :

```
Description

Cas d'utilisation

Permissions

Événements

API

Tests
```

---

# ✅ Checklist avant chaque Pull Request

* [ ] Tous les tests passent
* [ ] PHPStan OK
* [ ] Pint OK
* [ ] Rector OK
* [ ] Couverture stable
* [ ] README mis à jour
* [ ] Migration réversible
* [ ] Permissions créées
* [ ] Traductions ajoutées

---

# 🚀 Roadmap du MVP

## Phase 1

* [ ] Authentification
* [ ] Gestion des rôles
* [ ] Module Patients

---

## Phase 2

* [ ] Module Médecins
* [ ] Module Rendez-vous
* [ ] Calendrier

---

## Phase 3

* [ ] Module Consultations
* [ ] Dossier médical
* [ ] Historique

---

## Phase 4

* [ ] Prescriptions
* [ ] Ordonnances
* [ ] Pharmacie

---

## Phase 5

* [ ] Laboratoire
* [ ] Résultats
* [ ] Imagerie

---

## Phase 6

* [ ] Facturation
* [ ] Paiements
* [ ] Assurance

---

## Phase 7

* [ ] Notifications
* [ ] SMS
* [ ] Emails
* [ ] WhatsApp

---

## Phase 8

* [ ] Tableau de bord
* [ ] Rapports
* [ ] Statistiques

---

## Phase 9

* [ ] Installation de modules ZIP
* [ ] Marketplace
* [ ] Gestionnaire de plugins

---

# ⚠️ Anti-patterns à éviter

* [ ] Des services de plusieurs centaines de lignes
* [ ] Des contrôleurs contenant la logique métier
* [ ] Des modèles Eloquent qui font tout
* [ ] Des dépendances directes entre modules
* [ ] Des tests qui vérifient l'implémentation au lieu du comportement
* [ ] Des tests dépendants de services externes

---

# 📖 Sujets à maîtriser progressivement

## Priorité 1

* [ ] TDD
* [ ] PestPHP
* [ ] PHPUnit
* [ ] Laravel Testing

---

## Priorité 2

* [ ] Domain Driven Design
* [ ] Clean Architecture
* [ ] SOLID

---

## Priorité 3

* [ ] Event Driven Architecture
* [ ] CQRS
* [ ] Domain Events

---

## Priorité 4

* [ ] Mutation Testing
* [ ] GitHub Actions
* [ ] Performance Testing

---

# 💡 Conseils personnels

* Écrire le test **avant** le code.
* Les noms des tests doivent raconter une histoire métier.
* Une classe = une responsabilité.
* Préférer plusieurs petites classes plutôt qu'une classe gigantesque.
* Chaque module doit pouvoir évoluer indépendamment.
* Refactoriser régulièrement tant que tous les tests restent verts.
* Éviter les dépendances circulaires entre modules.
* Documenter les décisions importantes (Architecture Decision Records) pour comprendre pourquoi un choix a été fait plusieurs mois plus tard.
* Commencer petit : un module parfaitement conçu vaut mieux que dix modules difficiles à maintenir.

---

# 🎯 Objectif final

Construire une plateforme médicale :

* modulaire,
* facilement testable,
* extensible par plugins,
* maintenable sur plusieurs années,
* avec une couverture de tests élevée permettant d'ajouter des fonctionnalités sans crainte de régression.
