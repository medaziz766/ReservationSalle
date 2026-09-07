-- À exécuter une seule fois si la table reservation existe déjà.
ALTER TABLE reservation
    ADD COLUMN proposition_salle_id INT NULL AFTER type_demande,
    ADD COLUMN proposition_date_debut DATETIME NULL AFTER proposition_salle_id,
    ADD COLUMN proposition_date_fin DATETIME NULL AFTER proposition_date_debut,
    ADD CONSTRAINT fk_reservation_proposition_salle
        FOREIGN KEY (proposition_salle_id) REFERENCES salle(id) ON DELETE SET NULL;
