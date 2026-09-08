-- Base de données : reservation_system
CREATE DATABASE IF NOT EXISTS reservation_system CHARACTER SET utf8mb4;
USE reservation_system;

-- 1. Utilisateur (3 rôles : Admin, Gestionnaire, Utilisateur)
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin','Gestionnaire','Utilisateur') NOT NULL DEFAULT 'Utilisateur',
    date_creation DATE NOT NULL
);

-- 2. Bâtiment
CREATE TABLE batiment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(150) NOT NULL,
    nombre_etages INT NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL
);

-- 3. Salle
CREATE TABLE salle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    batiment_id INT NOT NULL,
    etage INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    capacite INT NOT NULL,
    equipements VARCHAR(255) DEFAULT '',
    statut ENUM('Disponible','Maintenance','Indisponible') NOT NULL DEFAULT 'Disponible',
    FOREIGN KEY (batiment_id) REFERENCES batiment(id) ON DELETE CASCADE
);

-- 4. Réservation
CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salle_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    objet VARCHAR(150) NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('En attente','Validée','Refusée','Annulée') NOT NULL DEFAULT 'En attente',
    type_demande ENUM('Création','Modification') NOT NULL DEFAULT 'Création',
    proposition_salle_id INT NULL,
    proposition_date_debut DATETIME NULL,
    proposition_date_fin DATETIME NULL,
    date_creation DATETIME NOT NULL,
    FOREIGN KEY (salle_id) REFERENCES salle(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (proposition_salle_id) REFERENCES salle(id) ON DELETE SET NULL
);

-- 5. Notification (boîte mail interne Utilisateur ↔ Gestionnaire)
CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    reservation_id INT NULL,
    destinataire_role ENUM('Utilisateur','Gestionnaire') NOT NULL,
    type VARCHAR(50) NOT NULL,
    message VARCHAR(255) NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservation(id) ON DELETE CASCADE
);

-- Données d'exemple
INSERT INTO utilisateur (nom, prenom, email, password, role, date_creation) VALUES
('Ben Ali', 'Sami', 'admin@rooms.tn', '$2y$10$examplehashvalueforadmin000000', 'Admin', '2026-01-05'),
('Trabelsi', 'Nour', 'gestionnaire@rooms.tn', '$2y$10$examplehashvalueforgest0000000', 'Gestionnaire', '2026-01-06'),
('Karray', 'Yassine', 'yassine@rooms.tn', '$2y$10$examplehashvalueforuser00000000', 'Utilisateur', '2026-01-10');

INSERT INTO batiment (nom, adresse, nombre_etages, latitude, longitude) VALUES
('Bâtiment A', '1 Rue André Ampère, El Ghazala', 4, 36.8987500, 10.1897200),
('Bâtiment B', '2 Rue des Sciences, El Ghazala', 3, 36.8991000, 10.1902500);

INSERT INTO salle (batiment_id, etage, nom, capacite, equipements, statut) VALUES
(1, 1, 'Salle Aurora', 12, 'Projecteur, Visioconférence, Tableau blanc', 'Disponible'),
(1, 2, 'Salle Atlas', 8, 'Écran TV, Tableau blanc', 'Disponible'),
(2, 1, 'Salle Nova', 20, 'Projecteur, Sonorisation', 'Maintenance');
