-- À exécuter une seule fois si la base reservation_system existe déjà.
CREATE TABLE IF NOT EXISTS notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,          -- le demandeur concerné par l'échange (contexte)
    reservation_id INT NULL,
    destinataire_role ENUM('Utilisateur','Gestionnaire') NOT NULL,
    type VARCHAR(50) NOT NULL,
    message VARCHAR(255) NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservation(id) ON DELETE CASCADE
);
