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

## Proposition de créneau alternatif (Gestionnaire ↔ Utilisateur)

Le Gestionnaire résout les conflits en **proposant** un autre créneau plutôt qu'en modifiant la réservation
directement (jugé plus professionnel : la décision finale revient à l'utilisateur) :

- `View/Back/proposerCreneau.php` — formulaire (salle + créneau), accessible depuis `demandes.php` et
  `conflits.php` (bouton **"proposer un créneau"**). Vérifie qu'il n'y a pas de conflit sur le créneau proposé.
- Les infos de la proposition sont stockées dans les colonnes `proposition_salle_id`,
  `proposition_date_debut`, `proposition_date_fin` de la table `reservation` (voir `sql/add_move_proposals.sql`) —
  la réservation d'origine n'est **pas modifiée** tant que l'utilisateur n'a pas répondu.
- Côté Utilisateur, `View/Front/mesReservations.php` affiche un bandeau "Le gestionnaire propose un nouveau
  créneau" avec deux liens : **Accepter** (la proposition remplace la réservation, statut → Validée) ou
  **Refuser** (la réservation d'origine passe en Refusée). Un email est envoyé à chaque étape
  (`Mailer::notifyPropositionCreneau/Acceptee/Refusee`).
- Tant qu'une proposition est en attente de réponse, la réservation n'apparaît plus avec les actions
  valider/refuser/modifier habituelles (pour éviter les actions concurrentes).

⚠️ Si ta base existe déjà, pense à exécuter `sql/add_move_proposals.sql` (une seule fois) si ce n'est pas
déjà fait.

## Boîte mail interne (notifications en base, en plus des emails)

En plus des emails envoyés via `Mailer`, chaque étape importante du cycle de vie d'une réservation crée
aussi une **notification en base** (table `notification`, voir `sql/add_notifications.sql`), consultable
directement dans l'application sans avoir besoin d'un serveur mail :

- **Boîte Utilisateur** (`View/Front/boiteMail.php`, lien "Boîte mail" dans le header) : reçoit les réponses
  du gestionnaire — validation, refus, proposition de créneau.
- **Boîte Gestionnaire** (`View/Back/boiteMail.php`, commune à tous les gestionnaires, lien dans la sidebar) :
  reçoit toutes les actions des utilisateurs — nouvelle demande, demande de modification, annulation,
  réponse à une proposition (acceptée/refusée).
- Un badge rouge (`.nav-badge`) affiche le nombre de messages non lus à côté du lien "Boîte mail", mis à jour
  à chaque chargement de page. Chaque message peut être marqué comme lu individuellement ou via
  "Tout marquer comme lu".

⚠️ Si ta base existe déjà, exécute `sql/add_notifications.sql` (une seule fois) pour créer la table.

## Gestion des conflits

`ReservationController::hasConflict()` vérifie, à chaque création ou modification, qu'aucune
réservation existante (statut `En attente` ou `Validée`) sur la même salle ne chevauche le
nouveau créneau (`date_debut < nouvelle_fin AND date_fin > nouveau_debut`). Le Gestionnaire
dispose en plus d'une vue `conflits.php` qui détecte les chevauchements déjà présents en base
(cas de double saisie manuelle) et permet de refuser l'une des deux réservations ou de lui
proposer un autre créneau/salle (voir section ci-dessus).

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

ALTER TABLE batiment
ADD COLUMN latitude DECIMAL(10,7) NOT NULL DEFAULT 36.8987500,
ADD COLUMN longitude DECIMAL(10,7) NOT NULL DEFAULT 10.1897200;
```
(Pas nécessaire si tu réimportes `sql/reservation_system.sql` en entier, les colonnes y sont déjà incluses.)

**Cercles de progression — taux d'utilisation par salle (Admin → Statistiques)**
Chaque salle affiche un cercle SVG (pur CSS/SVG, aucune librairie JS) représentant sa part dans le total des
réservations validées : `pourcentage = validées_salle / total_validées_toutes_salles × 100`. Couleur du cercle
selon le niveau : gris (<15%), orange (15-39%), vert (≥40%).

**Position GPS des bâtiments + petite carte dans les modals (OpenStreetMap / Leaflet)**
- `View/Back/addBatiment.php` et `updateBatiment.php` affichent une carte Leaflet : on clique dessus pour
  placer le bâtiment, ce qui remplit automatiquement les champs `latitude`/`longitude` (lecture seule, validés en JS).
- Côté FrontOffice, pas de page "Carte" dédiée : un bouton **"📍 Position"** sur chaque carte de salle
  (`View/Front/salles.php`) et sur la page de réservation (`View/Front/reserver.php`) ouvre une **petite fenêtre
  modale** avec une carte OpenStreetMap centrée sur le bâtiment (marqueur + popup avec le nom). Le modal est
  défini une seule fois dans `View/Front/footer.php` et réutilisé partout (`assets/js/mapModal.js`).
- Aucune clé API requise (tuiles OpenStreetMap gratuites), chargé via le CDN Leaflet (`unpkg.com/leaflet`).

**Mode jour/nuit**
Un bouton 🌙/☀️ (en haut à droite du FrontOffice, en bas de la sidebar du BackOffice — Admin et Gestionnaire)
bascule entre thème clair et sombre. Le choix est mémorisé dans le `localStorage` du navigateur
(`assets/js/theme.js` dans `Front/` et `Back/`), donc conservé d'une page à l'autre. Implémenté en CSS pur via
des variables (`:root[data-theme="dark"]`) — y compris les couleurs de fond des tableaux et des champs de
formulaire (`--table-header-bg`, `--input-bg`), qui restaient blanches par erreur dans une version précédente.


## Direction visuelle

Thème "signalétique de bâtiment" (plutôt que le bleu/blanc SaaS générique) : encre (`--ink`), papier
(`--paper`), un seul accent laiton (`--brass`) réservé aux actions et états actifs. Typographie **Archivo**
(titres, gras/condensé) + **IBM Plex Sans** (texte courant) + **IBM Plex Mono** (codes de salle, dates,
heures — pour un rendu "tableau d'affichage/horaires"). Coins peu arrondis (4-5px), bordures fines plutôt que
des ombres. Mode sombre géré via `:root[data-theme="dark"]` dans `admin.css` et `style.css`, avec les mêmes
noms de variables des deux côtés (Back/Front) pour rester cohérent.

## Installation (XAMPP / WAMP)



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
