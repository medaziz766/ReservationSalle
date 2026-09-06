# SalleReservationMVC — Système de Réservation de Salles de Réunion

Application PHP 8 en architecture MVC (PDO uniquement, sans framework) pour la gestion
et la réservation de salles de réunion, avec 3 rôles : **Admin Bâtiments**, **Gestionnaire
de Réservations** et **Utilisateur**.

## Entités
- **Utilisateur** (nom, prénom, email, mot de passe, rôle)
- **Bâtiment** (nom, adresse, nombre d'étages)
- **Salle** (bâtiment, étage, nom, capacité, équipements, statut)
- **Réservation** (salle, utilisateur, objet, date début/fin, statut)

## Structure du projet (MVC)

```
SalleReservationMVC/
├── config.php                 → connexion PDO + gestion de session (isLoggedIn, requireRole)
├── Model/                     → Utilisateur, Batiment, Salle, Reservation
├── Controller/
│   ├── AuthController.php     → register / login / logout
│   ├── UtilisateurController.php
│   ├── BatimentController.php
│   ├── SalleController.php    → CRUD + jointures + recherche + statistiques
│   └── ReservationController.php → CRUD + détection de conflits + recherche + rapports
├── Helper/
│   └── Mailer.php             → notifications email (+ log dans /logs/emails.log)
├── View/
│   ├── Auth/                  → login.php, register.php, logout.php
│   ├── Back/                  → espace commun Admin + Gestionnaire (un seul dossier,
│   │                             sidebar.php affiche le menu selon le rôle en session) :
│   │                             - Admin : Bâtiments, Salles, Maintenance, Statistiques, Rapports
│   │                             - Gestionnaire : Demandes, Réservation manuelle, Conflits, Recherche
│   └── Front/                 → espace Utilisateur/Client : Accueil, Salles (filtres),
│                                 Calendrier interactif, Réserver, Mes réservations
└── sql/reservation_system.sql → schéma + données d'exemple
```

Chaque page de `View/Back/` vérifie explicitement le rôle exact autorisé
(`requireRole('Admin')` ou `requireRole('Gestionnaire')`) en plus du contrôle
générique fait par `sidebar.php` — un Gestionnaire ne peut donc pas ouvrir une
page Admin même en tapant l'URL directement, et inversement.

## Fonctionnalités par rôle

**Admin Bâtiments** (`View/Back`)
- CRUD Bâtiments et Salles (capacité, équipements, localisation)
- Gestion de la maintenance/disponibilité des salles (`maintenance.php`)
- Statistiques d'utilisation par salle (`statistiques.php`)
- Rapports de réservation filtrés par période (`rapports.php`)

**Gestionnaire de Réservations** (`View/Back`)
- Validation / refus des demandes en attente (`demandes.php`)
- Création de réservations manuelles pour un utilisateur (`reservationManuelle.php`)
- Détection automatique des conflits + refus ou déplacement d'une réunion (`conflits.php`, `moveReservation.php`)
- Recherche multicritère : salle, statut, période, email (`recherche.php`)

**Utilisateur** (`View/Front`)
- Parcourir les salles disponibles avec filtres (capacité, bâtiment, équipement)
- **Calendrier interactif mensuel** par salle (navigation mois précédent/suivant, créneaux affichés)
- Soumettre une demande de réservation (email de confirmation envoyé)
- Modifier ou annuler ses réservations **jusqu'à 24h avant le début** (`mesReservations.php`)
- Historique complet de ses réservations avec statut (badge coloré)

## Gestion des conflits

`ReservationController::hasConflict()` vérifie, à chaque création ou modification, qu'aucune
réservation existante (statut `En attente` ou `Validée`) sur la même salle ne chevauche le
nouveau créneau (`date_debut < nouvelle_fin AND date_fin > nouveau_debut`). Le Gestionnaire
dispose en plus d'une vue `conflits.php` qui détecte les chevauchements déjà présents en base
(cas de double saisie manuelle) et permet de refuser l'une des deux réservations ou de déplacer
une réunion vers un autre créneau/salle.

## Emails

`Helper/Mailer.php` envoie les notifications (demande reçue, validée, refusée, annulée) via la
fonction native `mail()` de PHP **et** journalise systématiquement chaque email dans
`logs/emails.log`, ce qui permet de vérifier le contenu même si aucun serveur SMTP n'est
configuré en local. Pour un envoi réel en production, remplacer le corps de `Mailer::send()`
par PHPMailer + les identifiants SMTP de l'établissement.

## Nouvelles fonctionnalités

**Demande de modification (Utilisateur)**
Quand un utilisateur modifie une de ses réservations (`View/Front/updateReservationUser.php`), elle repasse
automatiquement en statut **« En attente »** avec `type_demande = 'Modification'`, et réapparaît dans
`View/Back/demandes.php` (badge distinctif "Création" / "Modification") pour re-validation par le Gestionnaire.
Un email de confirmation est envoyé (`Mailer::notifyDemandeModification`).

⚠️ **Migration base de données requise** si ta base existe déjà (créée avant cette mise à jour) :
```sql
ALTER TABLE reservation
ADD COLUMN type_demande ENUM('Création','Modification') NOT NULL DEFAULT 'Création' AFTER statut;
```
(Pas nécessaire si tu réimportes `sql/reservation_system.sql` en entier, la colonne y est déjà incluse.)

**Cercles de progression — taux d'utilisation par salle (Admin → Statistiques)**
Chaque salle affiche un cercle SVG (pur CSS/SVG, aucune librairie JS) représentant sa part dans le total des
réservations validées : `pourcentage = validées_salle / total_validées_toutes_salles × 100`. Couleur du cercle
selon le niveau : gris (<15%), orange (15-39%), vert (≥40%).

**Export PDF détaillé (Admin → Rapports)**
Le bouton **"Exporter en PDF"** génère un vrai fichier PDF via la librairie **FPDF** (`lib/fpdf/fpdf.php`,
licence libre, embarquable sans restriction — voir `lib/fpdf/LICENSE.txt`), avec le détail complet de chaque
réservation de la période (salle, bâtiment, dates, durée, statut) plus un résumé (totaux par statut) en bas
de page, sur autant de pages que nécessaire.



1. Copier `SalleReservationMVC` dans `htdocs` (XAMPP) ou `www` (WAMP).
2. Importer `sql/reservation_system.sql` dans phpMyAdmin (crée la base `reservation_system`).
3. Vérifier les identifiants dans `config.php` (`root` / mot de passe vide par défaut).
4. Créer un premier compte via :
   ```
   http://localhost/SalleReservationMVC/View/Auth/register.php
   ```
   (le rôle par défaut est `Utilisateur`, ce qui donne accès à `View/Front/`).
5. **Pour tester les rôles Admin / Gestionnaire** : dans phpMyAdmin, table `utilisateur`,
   modifier la colonne `role` du compte créé en `Admin` ou `Gestionnaire`. Ces deux rôles
   donnent accès à `View/Back/`, avec un menu différent selon le rôle exact.
   *(Les comptes de démonstration insérés par le script SQL ont des mots de passe factices
   et ne permettent pas de se connecter directement — passez par l'inscription puis changez
   le rôle en base.)*
6. Se connecter :
   ```
   http://localhost/SalleReservationMVC/View/Auth/login.php
   ```
   La redirection après connexion se fait automatiquement vers le bon espace selon le rôle.

## Contraintes respectées
- Architecture **MVC** stricte, interface **PDO** uniquement (aucun MySQLi).
- CRUD complet sur les 4 entités avec **jointures** (salle+bâtiment, réservation+salle+utilisateur).
- Contrôles de saisie **JavaScript** (aucun attribut de validation HTML natif type `required`/`pattern`).
- **Calendrier interactif** de disponibilité par salle.
- **Détection et gestion des conflits/chevauchements**.
- Templates **responsifs** (Front et BackOffice, cf. media queries dans les CSS).
- **Notifications email** (création, validation, refus, annulation).
- Aucun framework utilisé.
