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

