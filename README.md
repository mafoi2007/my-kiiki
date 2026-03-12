# Application de gestion des notes scolaires (Laravel 10)

Cette application implémente une base fonctionnelle pour un établissement secondaire avec authentification par **login/mot de passe** (sans email), gestion des rôles et modules métiers.

## Rôles pris en charge

`cellule_informatique`: CRUD classes, matières, utilisateurs, élèves (base) + gestion centrale.
- `chef_etablissement`: consultation (menu dédié sur dashboard).
- `censeur`: statistiques / visualisation des notes (menu dédié sur dashboard).
- `surveillant_general`: saisie absences (structure base de données prête).
- `econome`: paiements + solvabilité (structure base de données prête).
- `enseignant`: saisie des notes selon affectations (structure base de données prête).
- `parent`: consultation et module communication (messages).

## Règles de gestion intégrées dans le schéma

- Un enseignant peut avoir plusieurs affectations.
- Dans une classe, une matière n'a qu'un seul enseignant (`teacher_assignments` unique sur `school_class_id + subject_id`).
- Jusqu'à 6 évaluations par classe (modélisées via `evaluations.sequence_number`).
- Notes séquentielles/trimestrielles/annuelles modélisables via `grades` + agrégations.

## Installation

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Compte initial

Créé par le seeder:

- Login: `admin`
- Mot de passe: `admin1234`
- Rôle: `cellule_informatique`

## Stack UI

- Blade + Bootstrap 5 (CDN) pour une interface simple et propre.