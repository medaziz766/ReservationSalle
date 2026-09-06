-- À exécuter une seule fois si la base reservation_system existe déjà.
ALTER TABLE batiment
ADD COLUMN latitude DECIMAL(10,7) DEFAULT NULL AFTER nombre_etages,
ADD COLUMN longitude DECIMAL(10,7) DEFAULT NULL AFTER latitude;
