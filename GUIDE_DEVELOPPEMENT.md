# GUIDE DE DÉVELOPPEMENT - UMRah Management System (UMS)
## Version 2.0 - Plateforme SaaS de Gestion d'Agence Omra

---

## 📋 TABLE DES MATIÈRES

1. [Vue d'ensemble du projet](#vue-densemble)
2. [Architecture technique](#architecture-technique)
3. [Structure de la base de données](#base-de-données)
4. [Hiérarchie des rôles et permissions](#rôles-et-permissions)
5. [Modules fonctionnels](#modules-fonctionnels)
6. [Chatbot IA](#chatbot-ia)
7. [Dashboard Analytics](#dashboard-analytics)
8. [Design System](#design-system)
9. [Automatisations](#automatisations)
10. [Règles de développement strictes](#règles-de-développement)
11. [Plan de développement étape par étape](#plan-de-développement)
12. [Checklist de validation](#checklist)

---

## 🎯 VUE D'ENSEMBLE {#vue-densemble}

### Objectif du Projet
Système de gestion complète d'agences Omra couvrant l'intégralité du parcours pèlerin :
- Inscription → Dossier complet → Visa → Paiement → Voyage → Retour

### Objectifs Stratégiques
- ✅ Digitaliser et centraliser toutes les opérations d'une agence Omra
- ✅ Traçabilité complète du pèlerin (visa, paiement, hébergement, guide)
- ✅ Espace client intuitif avec chatbot intelligent multilingue
- ✅ Supervision ministérielle et contrôle inter-agences
- ✅ Automatisation des notifications, factures et rapports financiers
- ✅ Scalable pour adoption SaaS multi-agences et multi-branches

### Caractéristiques Clés
- **MODULAIRE** : Architecture en modules indépendants
- **ORIENTE SERVICES** : Service Layer obligatoire
- **SECURISE** : 2FA, Policies, Soft Deletes
- **SCALABLE** : Multi-agences, multi-branches
- **MVC STRICT** : Séparation stricte des responsabilités
- **CHATBOT IA** : Assistant intelligent multilingue

---

## 🏗️ ARCHITECTURE TECHNIQUE {#architecture-technique}

### Stack Technologique
- **Framework** : Laravel (version la plus récente)
- **Base de données** : MySQL
- **Cache** : Redis
- **Queue** : Redis/Database
- **Chatbot** : OpenAI GPT-4o-mini
- **PDF** : DomPDF
- **Frontend** : Blade + Chart.js + AJAX

### Structure MVC Laravel

```
app/
├── Models/              # Relations Eloquent, scopes, casts — AUCUNE logique métier
├── Http/
│   ├── Controllers/    # ≤ 5 lignes par méthode. Délègue au Service. Retourne view ou JSON
│   └── Requests/       # Toute validation dans FormRequest — JAMAIS dans Controller
│   └── Policies/       # $this->authorize() dans chaque action sensible
├── Services/            # TOUTE la logique métier. Injectés dans les Controllers
├── Repositories/        # Abstraction accès DB. Services passent par Repository
├── Jobs/                # Emails, SMS, PDF — traitement asynchrone OBLIGATOIRE
└── Observers/          # Audit log automatique sur created/updated/deleted
```

### Règles d'Architecture

| Couche | Règle Stricte |
|--------|---------------|
| **Models** | Relations Eloquent, scopes, casts — aucune logique métier |
| **Controllers** | ≤ 5 lignes par méthode. Délègue au Service. Retourne view ou JSON |
| **Services** | Toute la logique métier. Injectés dans les Controllers |
| **Repositories** | Abstraction accès DB. Services passent par Repository |
| **FormRequests** | Toute validation dans FormRequest — jamais dans Controller |
| **Policies** | $this->authorize() dans chaque action sensible |
| **Jobs/Queues** | Emails, SMS, PDF — traitement asynchrone obligatoire |
| **Observers** | Audit log automatique sur created/updated/deleted |

---

## 🗄️ BASE DE DONNÉES {#base-de-données}

### Schéma Principal

#### Table: `users`
```sql
- id (PK)
- name
- email (unique)
- password
- branch_id (FK → branches)
- role (Spatie Permission)
- active (boolean)
- 2fa_secret
- deleted_at (soft delete)
- timestamps
```

#### Table: `agencies`
```sql
- id (PK)
- name
- license_no (unique)
- ministry_status (enum: pending, approved, revoked)
- contact (JSON: phone, email, address)
- logo (string)
- deleted_at (soft delete)
- timestamps
```

#### Table: `branches`
```sql
- id (PK)
- agency_id (FK → agencies)
- name
- address
- phone
- manager_id (FK → users)
- deleted_at (soft delete)
- timestamps
```

#### Table: `pilgrims`
```sql
- id (PK)
- branch_id (FK → branches)
- agent_id (FK → users)
- package_id (FK → packages)
- passport_no (unique)
- first_name
- last_name
- email
- phone
- nationality
- status (enum: registered, dossier_complete, visa_submitted, visa_approved, departed, returned)
- deleted_at (soft delete)
- timestamps
```

#### Table: `packages`
```sql
- id (PK)
- branch_id (FK → branches)
- name
- type (enum: economic, standard, premium, vip)
- price (decimal)
- cost (decimal) # coût de base
- slots (integer) # capacité max
- slots_remaining (integer)
- departure_date (date)
- return_date (date)
- hotel_mecca_id (FK → hotels)
- hotel_medina_id (FK → hotels)
- nights_mecca (integer)
- nights_medina (integer)
- deleted_at (soft delete)
- timestamps
```

#### Table: `visas`
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- status (enum: not_submitted, submitted, processing, approved, refused)
- submitted_at (datetime)
- decision_at (datetime)
- expiry_date (date)
- refusal_reason (text, nullable)
- reference_no (string, nullable)
- documents (JSON) # upload documents consulaires
- timestamps
```

#### Table: `payments`
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- amount (decimal)
- method (enum: cash, transfer, tpe, mobile_money)
- status (enum: pending, completed, refunded)
- ref_no (string, unique) # numéro facture
- processed_by (FK → users)
- payment_date (date)
- created_at
- updated_at
```

#### Table: `commissions`
```sql
- id (PK)
- agent_id (FK → users)
- pilgrim_id (FK → pilgrims)
- amount (decimal)
- rate (decimal) # pourcentage
- status (enum: pending, paid)
- paid_at (datetime, nullable)
- timestamps
```

#### Table: `chat_sessions`
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- messages (JSON) # historique des messages
- lang (enum: fr, ar, en)
- escalated (boolean, default: false)
- escalated_to (FK → users, nullable)
- created_at
- updated_at
```

#### Table: `notifications`
```sql
- id (PK)
- user_id (FK → users)
- type (enum: email, sms, push)
- channel (enum: email, sms, push)
- content (text)
- sent_at (datetime, nullable)
- read_at (datetime, nullable)
- created_at
```

#### Table: `hotels` (supplémentaire)
```sql
- id (PK)
- name
- city (enum: mecca, medina)
- stars (integer)
- distance_haram (decimal) # en km
- deleted_at (soft delete)
- timestamps
```

#### Table: `pilgrim_documents` (supplémentaire)
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- type (enum: passport, photo, medical_certificate, other)
- file_path (string)
- uploaded_at
- timestamps
```

#### Table: `activity_logs` (supplémentaire pour timeline)
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- user_id (FK → users)
- action (string)
- description (text)
- metadata (JSON, nullable)
- created_at
```

### Indexation Obligatoire
- ✅ Toutes les foreign keys
- ✅ Colonnes `status` (pilgrims, visas, payments)
- ✅ Colonnes `created_at` (pour tri et filtres)
- ✅ `email` dans users (unique)
- ✅ `passport_no` dans pilgrims (unique)
- ✅ `ref_no` dans payments (unique)

### Soft Deletes
- ✅ **OBLIGATOIRE** sur toutes les tables principales :
  - users, agencies, branches, pilgrims, packages, hotels
- ✅ Utiliser `deleted_at` avec `SoftDeletes` trait

### Tables liées aux 4 rôles (agence, ministère, guide, pèlerin)

#### Table: `users` (champs additionnels)
```sql
- agence_id (FK → agencies, nullable) — pour guide et pèlerin
- activation_code (string, nullable)
- activation_code_expires_at (timestamp, nullable)
- activated_at (timestamp, nullable)
-- Rôle géré par Spatie (role_has_*), pas de colonne role
```

#### Table: `agencies` (champs additionnels)
```sql
- validated (boolean, default false)
- validated_by (FK → users, nullable)
- validated_at (timestamp, nullable)
```

#### Table: `groups`
```sql
- id (PK)
- agency_id (FK → agencies)
- name (string)
- timestamps
- index(agency_id)
```

#### Table: `guides`
```sql
- id (PK)
- user_id (FK → users, unique) — un utilisateur = un guide
- agency_id (FK → agencies) — un guide n'appartient qu'à une agence
- group_id (FK → groups, nullable) — groupe assigné
- timestamps
- index(agency_id), index(group_id)
```

#### Table: `pilgrims` (champs additionnels)
```sql
- user_id (FK → users, nullable) — si pèlerin inscrit via espace client
- agence_id (FK → agencies) — agence qui gère le pèlerin
- group_id (FK → groups, nullable)
- guide_id (FK → guides, nullable)
- index(agence_id), index(group_id), index(guide_id)
```

#### Table: `checkins`
```sql
- id (PK)
- pilgrim_id (FK → pilgrims)
- guide_id (FK → guides)
- type (enum: 'checkin', 'checkout')
- created_at (timestamp)
- index(pilgrim_id), index(guide_id)
```

---

## 👥 RÔLES ET PERMISSIONS {#rôles-et-permissions}

### Règles d'inscription et de création des comptes

| Acteur | Création du compte | Validation / Activation |
|--------|--------------------|-------------------------|
| **Ministère** | Créé **automatiquement** (seeder ou commande) — pas d'inscription publique | N/A |
| **Agence** | Inscription avec **email valide** (formulaire public) | Doit être **validée par le ministère** avant accès complet |
| **Guide** | **Créé par l'agence** — un guide n'appartient **qu'à une seule agence** | Compte activé par l'agence |
| **Pèlerin** | Inscription avec **email valide** (formulaire public) | Reçoit un **code d'activation par email** ; notifications par **email** et **dans le système** |

### Hiérarchie des Rôles — 4 rôles uniquement

Le système ne comporte **que ces 4 rôles** : `agence`, `ministere`, `guide`, `pelerin`. Tous les anciens rôles (Super Admin Agence, Admin Branche, Agent Commercial, etc.) sont supprimés.

---

### 1. AGENCE

**Accès complet à sa propre gestion uniquement** (données filtrées par `agence_id`).

| Module | Droits |
|--------|--------|
| **Comptabilité** | Gestion des paiements des pèlerins, suivi des transactions, rapports financiers de l'agence, états des comptes |
| **Visa** | Demandes de visa pour ses pèlerins, suivi des statuts, upload des documents requis, historique des visas |
| **Guides** | Gestion de **ses** guides : création / modification / suppression, assignation aux groupes, planning (un guide n'appartient qu'à une agence) |
| **Pèlerins** | Gestion complète de **ses** pèlerins : inscription, modification, assignation aux groupes, suivi des paiements, export des listes |

**Dashboard Agence** : Statistiques de l'agence (nombre de pèlerins, guides, groupes), état des visas en cours, situation comptable, alertes et notifications.

**Menu Agence** : Dashboard · Pèlerins · Guides · Comptabilité · Visas

---

### 2. MINISTÈRE

**Supervision et validation** — pas de modification directe des données opérationnelles.

| Module | Droits |
|--------|--------|
| **Audit** | Vue globale de toutes les agences, rapports d'audit, statistiques générales, traçabilité des actions |
| **Gestion des agences** | Liste de toutes les agences, **validation / activation** des agences, suspension / désactivation, visualisation des infos, historique par agence |
| **Validation des visas** | Liste de toutes les demandes, approbation / rejet, filtrage par agence, export des rapports |
| **Pèlerins (vue globale)** | Consultation de tous les pèlerins par agence, filtres (agence, statut, date), statistiques par agence — **pas de modification directe** |

**Dashboard Ministère** : Nombre total d'agences (actives / inactives), nombre total de pèlerins par agence, demandes de visa en attente, statistiques globales, graphiques et rapports.

**Menu Ministère** : Dashboard · Agences · Validation Visas · Pèlerins (Global) · Audit

**Création** : Le ministère est **créé automatiquement** (seeder ou commande Artisan), pas d'inscription publique.

---

### 3. GUIDE

**Vue limitée à son groupe assigné uniquement.** Le guide est **créé par l'agence** et **n'appartient qu'à une seule agence**.

| Module | Droits |
|--------|--------|
| **Dashboard** | Informations de **son groupe** uniquement : nombre de pèlerins, planning des activités, check-in / check-out du jour |
| **Pèlerins de son groupe** | Liste des pèlerins de son groupe, détails en **lecture seule**, **Check-in (arrivée)** et **Check-out (départ)** uniquement ; vue de l'état de présence. **Aucune** modification des informations personnelles, aucun ajout/suppression de pèlerins |

**Restrictions** : Ne voit **que** son groupe ; ne peut pas voir les autres guides ni les autres groupes. Actions limitées : check-in / check-out uniquement.

**Menu Guide** : Dashboard · Mon Groupe

---

### 4. PÈLERIN

**Vue personnelle uniquement** — lecture seule de ses propres données.

| Module | Droits |
|--------|--------|
| **Dashboard personnel** | Ses informations, statut de son visa, son groupe assigné, son guide, état de ses paiements |
| **Consultation** | Voir ses documents, son planning, notifications personnelles (par **email** et **dans le système**) |

**Restrictions** : Aucune modification possible ; vue lecture seule.

**Inscription** : Inscription avec **email valide** ; réception du **code d'activation par email** ; notifications par **email** et **dans le système**.

**Menu Pèlerin** : Mon Profil

---

### Permissions techniques (backend)

```php
// Constantes (ex. config/roles.php ou Modèle User)
const ROLES = ['agence', 'ministere', 'guide', 'pelerin'];

const PERMISSIONS = [
    'agence' => [
        'manage_own_pilgrims', 'manage_own_guides', 'manage_own_accounting',
        'manage_own_visas', 'view_own_data'
    ],
    'ministere' => [
        'audit_all', 'manage_agencies', 'validate_agencies', 'validate_visas',
        'view_all_pilgrims', 'view_statistics'
    ],
    'guide' => [
        'view_own_group', 'checkin_checkout', 'view_group_pilgrims'
    ],
    'pelerin' => ['view_own_data']
];
```

### Routes par rôle (Laravel)

- **Agence** : `agence/dashboard`, `agence/pilgrims`, `agence/guides`, `agence/accounting`, `agence/visas`
- **Ministère** : `ministere/dashboard`, `ministere/agencies`, `ministere/agencies/{id}/validate`, `ministere/visas`, `ministere/visas/{id}/validate`, `ministere/pilgrims`, `ministere/audit`
- **Guide** : `guide/dashboard`, `guide/group`, `guide/pilgrims`, `guide/checkin/{pilgrimId}`, `guide/checkout/{pilgrimId}`
- **Pèlerin** : `pelerin/dashboard`, `pelerin/profile`

### Middlewares

- **CheckRole(allowedRoles)** : vérifie que `req->user()->role` (ou Spatie) est dans la liste ; sinon 403.
- **CheckPermission(permission)** : vérifie que l'utilisateur a la permission ; sinon 403.
- Toujours filtrer en base : **agence** par `agence_id`, **guide** par `group_id` (et donc par son `guide_id`).

### Schéma base de données (extrait)

- **users** : `id`, `email`, `password`, `role` (Spatie), `agence_id` (nullable), `active`, `activation_code`, `activation_code_expires_at`, `activated_at`, …
- **agencies** : `id`, `name`, `validated` (boolean), `validated_by` (FK users), `validated_at`, …
- **groups** : `id`, `agency_id`, `name`, …
- **guides** : `id`, `user_id`, `agency_id`, `group_id` (groupe assigné)
- **pilgrims** : `id`, `user_id` (nullable), `agence_id`, `group_id`, `guide_id`, …
- **checkins** : `id`, `pilgrim_id`, `guide_id`, `type` (checkin/checkout), `created_at`
- **Audit** : traçabilité (qui, quoi, quand) sur actions sensibles.

### Implémentation

- Utiliser **Spatie Laravel Permission** avec les 4 rôles uniquement.
- Créer des **Policies** pour chaque ressource et vérifier avec `$this->authorize()`.
- **JAMAIS** de vérification manuelle des rôles dans les vues ; utiliser `@can` / `@role` ou Policies.
- **Isolation des données** : requêtes Agence filtrées par `agence_id` ; Guide filtrées par `group_id` (son groupe).

---

## 📦 MODULES FONCTIONNELS {#modules-fonctionnels}

### 4.1 — Gestion des Pèlerins

#### Fonctionnalités
- ✅ CRUD complet avec upload multi-documents
  - Passeport (scan)
  - Photos (2 formats)
  - Certificat médical
  - Autres documents requis par nationalité
- ✅ Timeline d'activité
  - Chaque action tracée avec timestamp et auteur
  - Table `activity_logs` avec Observer
- ✅ Statuts avec workflow
  - `registered` → `dossier_complete` → `visa_submitted` → `departed` → `returned`
- ✅ Recherche avancée
  - Filtres : statut, branche, agent, date, nationalité
  - Recherche texte : nom, email, passeport
- ✅ Export Excel/PDF
  - Liste pèlerins avec filtres appliqués

#### Structure de Fichiers
```
app/
├── Models/Pilgrim.php
├── Http/Controllers/PilgrimController.php
├── Http/Requests/
│   ├── StorePilgrimRequest.php
│   └── UpdatePilgrimRequest.php
├── Services/PilgrimService.php
├── Repositories/PilgrimRepository.php
├── Policies/PilgrimPolicy.php
└── Observers/PilgrimObserver.php
```

---

### 4.2 — Gestion des Forfaits

#### Fonctionnalités
- ✅ Types de forfaits
  - Économique / Standard / Premium / VIP
  - Grille tarifaire dynamique
- ✅ Association hôtels
  - Hôtels Mecca + Médine
  - Étoiles, distance Haram, nombre de nuits
- ✅ Calcul automatique du profit
  - `profit = prix_vente - coût_base - commissions`
- ✅ Gestion des slots
  - Capacité max configurable
  - Places restantes calculées automatiquement
  - Liste d'attente si complet
- ✅ Clonage de forfait
  - Pour la saison suivante

#### Structure de Fichiers
```
app/
├── Models/Package.php
├── Http/Controllers/PackageController.php
├── Services/PackageService.php
└── Repositories/PackageRepository.php
```

---

### 4.3 — Gestion des Visas

#### Fonctionnalités
- ✅ Statuts avec workflow
  - `not_submitted` → `submitted` → `processing` → `approved` / `refused`
- ✅ Alertes automatiques
  - J-30 expiration : notification agent + client
  - Refus : notification immédiate agent + client
- ✅ Upload documents consulaires
  - Tracking numéro de référence
- ✅ Statistiques
  - Taux d'acceptation
  - Délai moyen de traitement par lot

#### Structure de Fichiers
```
app/
├── Models/Visa.php
├── Http/Controllers/VisaController.php
├── Services/VisaService.php
└── Jobs/CheckVisaExpirationJob.php
```

---

### 4.4 — Module Financier

#### Fonctionnalités
- ✅ Paiements multiples
  - Cash, virement, TPE, mobile money
- ✅ Acomptes et échéanciers
  - Personnalisés par pèlerin
  - Alertes automatiques solde impayé J-15 avant départ
- ✅ Commissions agents
  - Taux configurable par agence
  - Calcul automatique à la validation
  - Tableau de bord commissions
- ✅ Rapports financiers
  - Mensuel : recettes, dépenses, bénéfice net
  - Forecast (prévisionnel)
  - Export PDF/Excel

#### Détails financiers
- Enregistrement et validation des paiements pèlerins
- Gestion des remboursements, avoirs et échéanciers
- Calcul automatique des commissions agents à la validation
- Génération de factures et reçus PDF numérotés automatiquement
- Rapports mensuels/annuels et forecast financier exportables

#### Structure de Fichiers
```
app/
├── Models/Payment.php
├── Models/Commission.php
├── Http/Controllers/PaymentController.php
├── Services/PaymentService.php
├── Services/CommissionService.php
└── Jobs/GenerateInvoiceJob.php
```

---

### 4.5 — Gestion Multi-Branches

#### Fonctionnalités
- ✅ Chaque branche = entité autonome
  - Rattachée à l'agence
- ✅ Isolation des données
  - Un agent ne voit que sa branche
  - Scope automatique dans Repository
- ✅ Consolidation Agence
  - Vue agrégée toutes branches
- ✅ Transfer de pèlerin
  - Entre branches avec log de traçabilité

#### Implémentation
- Middleware `BranchScope` pour filtrer automatiquement
- Scope Eloquent `scopeBranch()` dans tous les Models
- Policy pour vérifier accès branche

---

### 4.6 — Supervision Ministérielle

#### Fonctionnalités
- ✅ Dashboard national
  - Statistiques consolidées de toutes les agences
- ✅ Validation / révocation de licences
  - Statut agence : pending, approved, revoked
- ✅ Export rapports nationaux
  - PDF/Excel pour archivage officiel
- ✅ Aucune modification possible
  - Lecture seule sur données opérationnelles

---

## 🤖 CHATBOT IA {#chatbot-ia}

### Architecture Technique

#### Composants
| Composant | Technologie | Rôle |
|-----------|-------------|------|
| Moteur NLP | OpenAI GPT-4o-mini | Compréhension du langage naturel |
| ChatbotService.php | Laravel Service | Orchestration : intent, contexte, fallback |
| KnowledgeBaseService.php | Laravel Service | Recherche dans la FAQ Omra structurée |
| PilgrimContextService.php | Laravel Service | Injection données client (visa, paiement) dans le prompt |
| chat_sessions | MySQL | id, pilgrim_id, messages (JSON), lang, escalated |

### Fonctionnalités
- ✅ Auto-détection de langue (Français, Arabe, Anglais)
- ✅ Escalade humaine si score < 0.7
  - Transfert automatique vers agent avec contexte
- ✅ Scénarios couverts :
  - Processus Omra étape par étape
  - Requêtes contextuelles (données temps réel)

### Scénarios Couverts

#### Processus Omra étape par étape
1. Inscription et constitution du dossier (documents requis par nationalité)
2. Demande et obtention du visa Omra (délais, conditions, procédure de refus)
3. Paiement : acompte, solde, échéances disponibles
4. Préparation voyage : vaccinations, bagages, tenue Ihram
5. Accomplissement des rites : Tawaf, Sa'i, Halq, Umrah complète

#### Requêtes contextuelles (données temps réel)
- "Où en est mon visa ?" → statut réel depuis la table visas
- "Combien me reste-t-il à payer ?" → solde depuis le module financier
- "Quels documents me manquent ?" → check-list personnalisée par dossier
- "Mon hôtel est-il confirmé ?" → informations réservation en direct

### Structure de Fichiers
```
app/
├── Services/
│   ├── ChatbotService.php
│   ├── KnowledgeBaseService.php
│   └── PilgrimContextService.php
├── Http/Controllers/ChatbotController.php
└── Models/ChatSession.php
```

---

## 📊 DASHBOARD ANALYTICS {#dashboard-analytics}

### KPI Cards — 4 Indicateurs Clés

| KPI | Calcul | Delta affiché |
|-----|--------|---------------|
| **Total Pèlerins** | COUNT tous statuts | vs mois précédent |
| **Revenu Mensuel** | SUM paiements validés du mois | vs mois précédent |
| **Taux Visa (%)** | Acceptés / Total déposés × 100 | vs trimestre précédent |
| **Groupes Actifs** | Forfaits départ dans 30 jours | vs même période an dernier |

### Graphiques Chart.js
- ✅ **Line Chart** — Revenue 12 mois glissants
  - Multi-séries : recettes vs dépenses
- ✅ **Donut Chart** — Distribution visas
  - Accepté / En cours / Refusé / Non déposé
- ✅ **Bar Chart** — Pèlerins par branche
  - Comparatif mensuel
- ✅ **Gauge Chart** — Taux de remplissage forfaits actifs

### Optimisations Performance
- ✅ **Cache Redis** 5 minutes sur toutes les requêtes agrégées
- ✅ **Eager Loading** systématique — interdiction absolue du N+1
- ✅ **Indexation** : toutes les foreign keys + status + created_at
- ✅ **Agrégations côté DB** (SUM, COUNT, AVG) — aucun calcul PHP
- ✅ **Réponse JSON** pour AJAX : rechargement partiel des widgets sans reload page

### Structure de Fichiers
```
app/
├── Http/Controllers/DashboardController.php
├── Services/DashboardService.php
└── Repositories/DashboardRepository.php
```

---

## 🎨 DESIGN SYSTEM {#design-system}

### Palette de Couleurs

| Couleur | Code | Usage |
|---------|------|-------|
| Primary Green | `#0F3F2E` | Sidebar, headers, boutons |
| Gold Accent | `#C9A227` | Accents, KPI, bordures |
| Dark Green | `#0B2C21` | Hover, footer, sidebar dark |
| Success | `#22C55E` | Visa accepté, paiement OK |
| Warning | `#F59E0B` | Dossier incomplet, en cours |
| Danger | `#EF4444` | Visa refusé, retard, alerte |

### Composants UI
- ✅ **Sidebar verticale fixe** (240px) avec mini-mode collapsible (64px)
- ✅ **Cards arrondies** : border-radius 16px, shadow-md, hover: shadow-lg
- ✅ **Typographie** : Poppins (titres) + Inter (corps)
- ✅ **Tables** : sticky header, tri par colonne, pagination 15 items/page

---

## 🔔 AUTOMATISATIONS {#automatisations}

### Notifications Automatiques

| Événement | Canal | Destinataire |
|-----------|-------|--------------|
| Inscription pèlerin validée | Email + SMS | Pèlerin + Agence |
| Dossier complet reçu | Email | Agence + Pèlerin |
| Visa déposé | Email + Push | Pèlerin + Agence |
| Visa accepté / refusé | Email + SMS + Push | Pèlerin + Agence |
| Paiement reçu | Email (reçu PDF) | Pèlerin + Agence |
| Solde impayé J-15 | Email + SMS | Pèlerin + Agence |
| Départ J-7 | Email (guide complet) | Pèlerin + Guide |
| Expiration visa imminente | Email | Agence + Pèlerin |
| Retour du pèlerinage | Email (questionnaire) | Pèlerin |

### Documents Générés Automatiquement
- ✅ **Contrat PDF** signé électroniquement (DomPDF) à la validation inscription
- ✅ **Facture PDF** numérotée automatiquement à chaque paiement
- ✅ **Voucher hébergement** avec QR code de check-in (envoyé J-7)
- ✅ **Rapport financier mensuel PDF** généré le 1er de chaque mois via Queue

### Jobs à Créer
```
app/Jobs/
├── SendPilgrimRegistrationNotificationJob.php
├── SendDossierCompleteNotificationJob.php
├── SendVisaStatusNotificationJob.php
├── SendPaymentReceiptJob.php
├── SendBalanceReminderJob.php
├── SendDepartureGuideJob.php
├── CheckVisaExpirationJob.php
├── SendReturnQuestionnaireJob.php
├── GenerateContractJob.php
├── GenerateInvoiceJob.php
└── GenerateMonthlyFinancialReportJob.php
```

---

## ⚠️ RÈGLES DE DÉVELOPPEMENT STRICTES {#règles-de-développement}

### ✅ À RESPECTER ABSOLUMENT

| Règle | Description |
|-------|-------------|
| **Service Layer** | Toute logique métier dans Services |
| **FormRequest** | Toute validation dans FormRequest |
| **Eager Loading** | Systématique pour éviter N+1 |
| **Soft Deletes** | Sur toutes les tables principales |
| **Transactions DB** | Pour les paiements |
| **Policies** | Pour contrôle d'accès |
| **Queue** | Pour emails, SMS, PDF |
| **Cache Redis** | Sur agrégations dashboard |
| **Pagination** | Obligatoire (15 items/page) |
| **Indexation** | Sur FK, status, created_at |

### ❌ ABSOLUMENT INTERDIT

| Interdiction | Raison |
|--------------|--------|
| ✗ Logique métier dans Controllers | Violation MVC |
| ✗ Validation directe dans Controllers | Séparation des responsabilités |
| ✗ Lazy Loading (N+1 problem) | Performance |
| ✗ Suppression physique des données | Traçabilité |
| ✗ Écriture financière hors transaction | Intégrité données |
| ✗ Vérification manuelle des rôles | Sécurité |
| ✗ Envoi email synchrone | Performance |
| ✗ Requêtes brutes répétitives sans cache | Performance |
| ✗ Retourner toute une collection sans pagination | Performance |
| ✗ Colonnes filtrées non indexées | Performance |

---

## 📅 PLAN DE DÉVELOPPEMENT {#plan-de-développement}

### ⚠️ DURÉE TOTALE : 1 SEMAINE (7 JOURS) - DÉLAI URGENT
**Équipe** : 2 personnes (Vous + Moi) travaillant en parallèle
**Objectif** : Développement complet + Tests + Déploiement en production

> **NOTE IMPORTANTE** : Ce plan est intensif et nécessite un travail en parallèle sur plusieurs modules simultanément. Prioriser les fonctionnalités essentielles pour la mise en production. Avec seulement 2 personnes, nous devons être très efficaces et travailler en complémentarité.

---

## 📆 PLANNING JOURNALIER INTENSIF

### 🔴 JOUR 1 (LUNDI) : FONDATIONS & INFRASTRUCTURE

#### Matin (4h)
- [ ] **Installation Laravel** + dépendances (composer install)
- [ ] **Configuration base de données** MySQL (.env)
- [ ] **Configuration Redis** pour cache (si disponible, sinon utiliser database cache)
- [ ] **Configuration Queue** (database driver pour simplicité)
- [ ] **Installation packages** :
  - [ ] Spatie Laravel Permission
  - [ ] DomPDF
  - [ ] Maatwebsite Excel (export)
  - [ ] Intervention Image (upload images)

#### Après-midi (4h)
- [ ] **Création migrations** (toutes les tables en une fois) :
  - [ ] users, agencies, branches
  - [ ] pilgrims, packages, hotels
  - [ ] visas, payments, commissions
  - [ ] chat_sessions, notifications
  - [ ] activity_logs, pilgrim_documents
- [ ] **Création Models** avec relations Eloquent de base
- [ ] **Configuration Soft Deletes** sur tous les Models
- [ ] **Seeders** : rôles, permissions, agence test, utilisateur admin

#### Soir (2h)
- [ ] **Système d'authentification** Laravel (login/register)
- [ ] **Configuration Spatie Permissions** de base
- [ ] **Middleware BranchScope** (isolation branches)

**Livrable J1** : Base de données opérationnelle + Auth fonctionnelle

---

### 🟠 JOUR 2 (MARDI) : AUTHENTIFICATION & RÔLES + LAYOUT

#### Matin (4h)
- [ ] **Création rôles et permissions** (les 4 rôles : Agence, Ministère, Guide, Pèlerin)
- [ ] **Policies de base** (PilgrimPolicy, PackagePolicy, VisaPolicy, PaymentPolicy)
- [ ] **Interface gestion utilisateurs** (CRUD avec assignation rôles)
- [ ] **2FA basique** (optionnel si temps manque, peut être ajouté post-déploiement)

#### Après-midi (4h)
- [ ] **Dashboard Layout** :
  - [ ] Sidebar verticale (240px) avec menu
  - [ ] Header avec profil utilisateur
  - [ ] Footer
  - [ ] Layout responsive
- [ ] **Design System** : intégration couleurs (Primary Green #0F3F2E, Gold #C9A227, etc.)
- [ ] **Composants UI** : Cards, Tables, Buttons (classes Tailwind/Bootstrap)

#### Soir (2h)
- [ ] **Tests authentification** par rôle
- [ ] **Middleware** vérification permissions sur routes

**Livrable J2** : Auth complète + Layout dashboard + Permissions fonctionnelles

---

### 🟡 JOUR 3 (MERCREDI) : MODULE PÈLERINS + FORFAITS

#### Matin (4h) - Pèlerins
- [ ] **CRUD Pèlerins** :
  - [ ] Models, Controllers, Services, Repositories
  - [ ] FormRequests (StorePilgrimRequest, UpdatePilgrimRequest)
  - [ ] Policies d'accès
- [ ] **Upload multi-documents** (passeport, photos, certificat médical)
- [ ] **Gestion statuts** (workflow : registered → dossier_complete → visa_submitted → departed → returned)

#### Après-midi (4h) - Forfaits + Timeline
- [ ] **CRUD Forfaits** :
  - [ ] Models, Controllers, Services
  - [ ] Association hôtels (Mecca + Médine)
  - [ ] Calcul automatique profit
  - [ ] Gestion slots
- [ ] **Timeline d'activité** : Observer sur Pilgrim pour activity_logs
- [ ] **Interface liste pèlerins** : table avec pagination (15 items/page)

#### Soir (2h)
- [ ] **Recherche avancée** pèlerins (filtres : statut, branche, agent, date, nationalité)
- [ ] **Export Excel** liste pèlerins (basique)

**Livrable J3** : Module Pèlerins fonctionnel + Module Forfaits fonctionnel

---

### 🟢 JOUR 4 (JEUDI) : MODULE VISAS + FINANCIER

#### Matin (4h) - Visas
- [ ] **CRUD Visas** :
  - [ ] Models, Controllers, Services
  - [ ] Workflow statuts (not_submitted → submitted → processing → approved/refused)
  - [ ] Upload documents consulaires
  - [ ] Tracking référence
- [ ] **Job vérification expiration** (J-30) - Schedule dans Kernel.php

#### Après-midi (4h) - Financier
- [ ] **CRUD Paiements** :
  - [ ] Models, Controllers, Services
  - [ ] Méthodes paiement (cash, transfer, tpe, mobile_money)
  - [ ] **Transactions DB** obligatoires
- [ ] **Génération factures PDF** numérotées automatiquement
- [ ] **Calcul commissions agents** (automatique à validation paiement)

#### Soir (2h)
- [ ] **Système échéanciers** personnalisés (basique)
- [ ] **Job alerte solde impayé** (J-15) - Schedule

**Livrable J4** : Module Visas fonctionnel + Module Financier fonctionnel

---

### 🔵 JOUR 5 (VENDREDI) : MULTI-BRANCHES + CHATBOT IA

#### Matin (4h) - Multi-Branches
- [ ] **Isolation données par branche** :
  - [ ] Scope Eloquent sur tous les Models
  - [ ] Middleware BranchScope actif
- [ ] **Transfer pèlerin** entre branches (avec log)
- [ ] **Consolidation Agence** (vue agrégée toutes branches)

#### Après-midi (4h) - Chatbot IA
- [ ] **Intégration OpenAI API** (GPT-4o-mini)
- [ ] **ChatbotService** (orchestration)
- [ ] **KnowledgeBaseService** (FAQ Omra structurée)
- [ ] **PilgrimContextService** (injection données temps réel)
- [ ] **Interface chat client** (basique)

#### Soir (2h)
- [ ] **Auto-détection langue** (FR/AR/EN)
- [ ] **Escalade humaine** (si score < 0.7)
- [ ] **Scénarios de base** (statut visa, solde restant)

**Livrable J5** : Multi-Branches fonctionnel + Chatbot IA opérationnel

---

### 🟣 JOUR 6 (SAMEDI) : DASHBOARD + AUTOMATISATIONS

#### Matin (4h) - Dashboard Analytics
- [ ] **KPI Cards** (4 indicateurs) :
  - [ ] Total Pèlerins (vs mois précédent)
  - [ ] Revenu Mensuel (vs mois précédent)
  - [ ] Taux Visa % (vs trimestre précédent)
  - [ ] Groupes Actifs (vs année dernière)
- [ ] **Graphiques Chart.js** :
  - [ ] Line Chart revenus 12 mois
  - [ ] Donut Chart distribution visas
  - [ ] Bar Chart pèlerins par branche
  - [ ] Gauge Chart remplissage forfaits

#### Après-midi (4h) - Automatisations
- [ ] **Jobs notifications** (priorité aux plus importantes) :
  - [ ] Inscription pèlerin validée
  - [ ] Visa accepté/refusé
  - [ ] Paiement reçu
  - [ ] Solde impayé J-15
  - [ ] Départ J-7
- [ ] **Templates emails** (basiques mais fonctionnels)
- [ ] **Génération documents PDF** :
  - [ ] Contrat à validation inscription
  - [ ] Facture à chaque paiement
  - [ ] Voucher hébergement J-7

#### Soir (2h)
- [ ] **Cache Redis** sur agrégations dashboard (5 min)
- [ ] **Optimisations** : Eager Loading systématique
- [ ] **AJAX** rechargement partiel dashboard

**Livrable J6** : Dashboard complet + Automatisations essentielles

---

### 🔴 JOUR 7 (DIMANCHE) : SUPERVISION + TESTS + DÉPLOIEMENT

#### Matin (3h) - Supervision Ministérielle
- [ ] **Dashboard supervision** (statistiques consolidées toutes agences)
- [ ] **Validation/révocation licences** agences
- [ ] **Export rapports nationaux** PDF/Excel
- [ ] **Policies lecture seule** pour superviseurs

#### Après-midi (4h) - Tests & Corrections
- [ ] **Tests fonctionnels** :
  - [ ] Authentification par rôle
  - [ ] CRUD Pèlerins
  - [ ] CRUD Forfaits
  - [ ] CRUD Visas
  - [ ] CRUD Paiements
  - [ ] Chatbot (scénarios de base)
- [ ] **Corrections bugs** critiques
- [ ] **Vérification permissions** (les 4 rôles)

#### Soir (3h) - Déploiement Production
- [ ] **Configuration production** (.env production)
- [ ] **Optimisations production** :
  - [ ] APP_DEBUG=false
  - [ ] Cache config, routes, views
  - [ ] Optimisation autoloader
- [ ] **Migration base de données** production
- [ ] **Déploiement** (serveur/hébergement)
- [ ] **Tests post-déploiement** (vérification fonctionnalités essentielles)
- [ ] **Backup** base de données

**Livrable J7** : Projet complet déployé en production ✅

---

## ⚡ STRATÉGIE DE DÉVELOPPEMENT PARALLÈLE

### Répartition des Tâches (Équipe de 2 personnes)

#### 🤖 MOI (Auto - Backend + Architecture)
**Responsabilités principales** :
- Architecture et structure du projet
- Backend Core (Models, Migrations, Relations)
- Services et Repositories (logique métier)
- API et intégrations (Chatbot IA, OpenAI)
- Jobs et automatisations
- Tests backend

**Planning détaillé** :
- **Jours 1-2** : 
  - Infrastructure complète (Laravel setup, packages)
  - Toutes les migrations et Models avec relations
  - Configuration Spatie Permissions
  - Seeders (rôles, permissions, données test)
  - Services de base et Repositories
- **Jours 3-4** :
  - Services Pèlerins, Forfaits, Visas, Paiements
  - Jobs automatiques (notifications, alertes)
  - Intégration OpenAI pour Chatbot
  - ChatbotService, KnowledgeBaseService, PilgrimContextService
- **Jours 5-6** :
  - Dashboard Analytics (Services backend, agrégations)
  - Multi-Branches (scopes, middleware)
  - Supervision Ministérielle (backend)
  - Optimisations (cache, eager loading)
- **Jour 7** :
  - Tests backend complets
  - Corrections bugs critiques
  - Préparation déploiement (config, optimisations)

#### 👤 VOUS (Frontend + Intégration + UI)
**Responsabilités principales** :
- Interface utilisateur (Blade templates)
- Design System et composants UI
- Controllers (délégation aux Services)
- FormRequests et validation
- Policies et sécurité frontend
- Dashboard Charts (Chart.js)
- Tests frontend et intégration

**Planning détaillé** :
- **Jours 1-2** :
  - Layout dashboard (sidebar, header, footer)
  - Design System (couleurs, composants)
  - Système d'authentification (vues login/register)
  - Interface gestion utilisateurs
  - Controllers de base
- **Jours 3-4** :
  - Interfaces CRUD Pèlerins (liste, formulaire, upload)
  - Interfaces CRUD Forfaits
  - Interfaces CRUD Visas
  - Interfaces CRUD Paiements
  - FormRequests pour toutes les validations
  - Policies pour contrôle d'accès
- **Jours 5-6** :
  - Dashboard avec KPI Cards
  - Graphiques Chart.js (Line, Donut, Bar, Gauge)
  - Interface Chatbot (chat client)
  - Multi-Branches (sélecteur branche, filtres)
  - Export Excel/PDF (boutons et actions)
- **Jour 7** :
  - Finalisation UI/UX
  - Tests frontend
  - Vérification responsive
  - Documentation utilisateur basique

### 🔄 Coordination Quotidienne

**Chaque jour, synchronisation** :
- **Matin** : Briefing sur les tâches du jour
- **Milieu de journée** : Point d'avancement et ajustements
- **Soir** : Revue du code, tests d'intégration, planification jour suivant

**Communication** :
- Partage des Services créés (pour intégration dans Controllers)
- Partage des routes et endpoints
- Tests d'intégration continus
- Résolution rapide des conflits

---

## 🎯 PRIORISATION FONCTIONNALITÉS

### ✅ OBLIGATOIRE (MVP - Minimum Viable Product)
1. Authentification + Rôles + Permissions
2. CRUD Pèlerins (avec upload documents)
3. CRUD Forfaits
4. CRUD Visas (workflow statuts)
5. CRUD Paiements (avec factures PDF)
6. Dashboard avec KPI de base
7. Multi-Branches (isolation données)
8. Chatbot IA (version basique fonctionnelle)

### ⚠️ IMPORTANT (Si temps disponible)
- Supervision Ministérielle (dashboard consolidé)
- Export Excel/PDF avancé
- Timeline d'activité complète
- Tous les jobs notifications
- Forecast financier

### 📝 POST-DÉPLOIEMENT (Améliorations futures)
- 2FA complet
- Optimisations performance avancées
- Tests unitaires complets
- Documentation utilisateur détaillée
- Formation utilisateurs

---

## 🚨 POINTS D'ATTENTION CRITIQUES

### À NE JAMAIS OUBLIER
- ✅ **Transactions DB** pour tous les paiements
- ✅ **Policies** sur toutes les actions sensibles
- ✅ **Soft Deletes** sur toutes les tables principales
- ✅ **Eager Loading** pour éviter N+1
- ✅ **Queue** pour emails/PDF (même si synchrone en dev)
- ✅ **Validation** dans FormRequests uniquement

### Optimisations Rapides
- Cache Redis sur dashboard (même 1 min en dev)
- Indexation DB dès le début (FK, status, created_at)
- Pagination 15 items partout
- Limiter les requêtes N+1 dès le développement

---

## 📋 CHECKLIST FINALE AVANT DÉPLOIEMENT

### Fonctionnalités
- [ ] Authentification fonctionnelle (tous rôles)
- [ ] CRUD Pèlerins opérationnel
- [ ] CRUD Forfaits opérationnel
- [ ] CRUD Visas opérationnel
- [ ] CRUD Paiements opérationnel
- [ ] Dashboard affiche les KPI
- [ ] Chatbot répond aux questions de base
- [ ] Multi-Branches isolé correctement

### Sécurité
- [ ] APP_DEBUG=false en production
- [ ] Permissions vérifiées (Policies)
- [ ] Transactions DB sur paiements
- [ ] Validation stricte des inputs

### Performance
- [ ] Cache activé
- [ ] Eager Loading vérifié
- [ ] Indexation DB en place
- [ ] Pagination active

### Déploiement
- [ ] .env production configuré
- [ ] Migrations exécutées
- [ ] Seeders exécutés (rôles, admin)
- [ ] Backup DB effectué
- [ ] Tests post-déploiement OK

---

**⚠️ RAPPEL** : Ce planning est très serré. Travailler en parallèle, prioriser l'essentiel, et documenter ce qui sera amélioré post-déploiement.

---

## ✅ CHECKLIST DE VALIDATION {#checklist}

### Architecture
- [ ] Tous les Controllers délèguent aux Services
- [ ] Aucune logique métier dans les Models
- [ ] Toutes les validations dans FormRequests
- [ ] Tous les accès protégés par Policies
- [ ] Tous les emails/SMS/PDF en Queue

### Base de Données
- [ ] Soft Deletes sur toutes les tables principales
- [ ] Indexation sur toutes les FK, status, created_at
- [ ] Relations Eloquent correctement définies
- [ ] Migrations testées et rollback fonctionnel

### Performance
- [ ] Eager Loading systématique (pas de N+1)
- [ ] Cache Redis sur agrégations dashboard
- [ ] Pagination 15 items/page partout
- [ ] Agrégations côté DB (SUM, COUNT, AVG)

### Sécurité
- [ ] 2FA implémenté
- [ ] Policies sur toutes les actions sensibles
- [ ] Transactions DB pour paiements
- [ ] Validation stricte des inputs

### Fonctionnalités
- [ ] Tous les modules CRUD fonctionnels
- [ ] Workflow statuts respecté
- [ ] Notifications automatiques opérationnelles
- [ ] Chatbot IA fonctionnel avec escalade
- [ ] Dashboard avec tous les KPI et graphiques
- [ ] Export Excel/PDF fonctionnel

### Tests
- [ ] Tests unitaires Services
- [ ] Tests d'intégration Controllers
- [ ] Tests Policies
- [ ] Tests de charge

---

## 📝 NOTES IMPORTANTES

### Priorités de Développement
1. **Sécurité** : Toujours en premier (Policies, 2FA, validation)
2. **Performance** : Dès le début (eager loading, cache, indexation)
3. **Traçabilité** : Observers et activity_logs dès le début
4. **Scalabilité** : Architecture multi-branches dès le début

### Points d'Attention
- ⚠️ **JAMAIS** de logique métier dans les Controllers
- ⚠️ **TOUJOURS** utiliser des Transactions pour les opérations financières
- ⚠️ **TOUJOURS** vérifier les permissions avec Policies
- ⚠️ **TOUJOURS** utiliser Queue pour les notifications
- ⚠️ **TOUJOURS** paginer les listes

### Maintenance
- Logs d'audit automatiques via Observers
- Monitoring des performances (cache hit rate, query time)
- Backup automatique base de données
- Versioning API si évolution future

---

**Document créé le** : 2026
**Version du guide** : 2.0
**Dernière mise à jour** : Structure des 4 rôles (agence, ministère, guide, pèlerin) — inscription agence/pèlerin, validation ministère, guide créé par l'agence.
**Basé sur** : Cahier des Charges Technique & Fonctionnel UMS v2.0

---

*Ce guide doit être consulté à chaque étape du développement pour s'assurer du respect des spécifications.*

