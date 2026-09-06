<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Reservation.php';
require_once __DIR__ . '/../Helper/Mailer.php';

class ReservationController
{
    // Liste complète avec jointures (salle, bâtiment, utilisateur) pour affichage BackOffice
    public function listReservations()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT r.*, s.nom AS salle_nom, b.nom AS batiment_nom,
                              u.nom AS user_nom, u.prenom AS user_prenom, u.email AS user_email
                              FROM reservation r
                              JOIN salle s ON r.salle_id = s.id
                              JOIN batiment b ON s.batiment_id = b.id
                              JOIN utilisateur u ON r.utilisateur_id = u.id
                              ORDER BY r.date_debut DESC");
        return $stmt->fetchAll();
    }

    public function getReservationById($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT r.*, s.nom AS salle_nom, u.email AS user_email
                                FROM reservation r
                                JOIN salle s ON r.salle_id = s.id
                                JOIN utilisateur u ON r.utilisateur_id = u.id
                                WHERE r.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function listByUtilisateur($utilisateurId)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT r.*, s.nom AS salle_nom, b.nom AS batiment_nom
                                FROM reservation r
                                JOIN salle s ON r.salle_id = s.id
                                JOIN batiment b ON s.batiment_id = b.id
                                WHERE r.utilisateur_id = :uid
                                ORDER BY r.date_debut DESC");
        $stmt->execute(['uid' => $utilisateurId]);
        return $stmt->fetchAll();
    }

    // Historique filtré (statut et/ou salle) pour la page "Mes réservations"
    public function listByUtilisateurFiltre($utilisateurId, $statut = null, $salleId = null)
    {
        $pdo = config::getConnexion();
        $sql = "SELECT r.*, s.nom AS salle_nom, b.nom AS batiment_nom
                FROM reservation r
                JOIN salle s ON r.salle_id = s.id
                JOIN batiment b ON s.batiment_id = b.id
                WHERE r.utilisateur_id = :uid";
        $params = ['uid' => $utilisateurId];

        if (!empty($statut)) { $sql .= " AND r.statut = :statut"; $params['statut'] = $statut; }
        if (!empty($salleId)) { $sql .= " AND r.salle_id = :salle_id"; $params['salle_id'] = $salleId; }

        $sql .= " ORDER BY r.date_debut DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Réservations d'une salle donnée pour affichage calendrier (mois/année)
    public function listBySalleForCalendar($salleId, $year, $month)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM reservation
                                WHERE salle_id = :salle_id
                                AND statut IN ('En attente','Validée')
                                AND YEAR(date_debut) = :year AND MONTH(date_debut) = :month
                                ORDER BY date_debut ASC");
        $stmt->execute(['salle_id' => $salleId, 'year' => $year, 'month' => $month]);
        return $stmt->fetchAll();
    }

    // Détection de conflit : chevauchement de créneaux sur la même salle
    // (statuts "En attente" et "Validée" bloquent le créneau ; "Refusée"/"Annulée" libèrent le créneau)
    public function hasConflict($salleId, $dateDebut, $dateFin, $excludeReservationId = null)
    {
        $pdo = config::getConnexion();
        $sql = "SELECT COUNT(*) AS total FROM reservation
                WHERE salle_id = :salle_id
                AND statut IN ('En attente','Validée')
                AND date_debut < :date_fin
                AND date_fin > :date_debut";
        $params = ['salle_id' => $salleId, 'date_debut' => $dateDebut, 'date_fin' => $dateFin];

        if ($excludeReservationId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'] > 0;
    }

    // Création d'une demande de réservation (front office ou manuelle par le gestionnaire)
    public function createReservation(Reservation $r)
    {
        if ($this->hasConflict($r->getSalleId(), $r->getDateDebut(), $r->getDateFin())) {
            return "Conflit : la salle est déjà réservée sur ce créneau.";
        }

        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO reservation (salle_id, utilisateur_id, objet, date_debut, date_fin, statut, date_creation)
                                VALUES (:salle_id, :utilisateur_id, :objet, :date_debut, :date_fin, :statut, :date_creation)");
        $stmt->execute([
            'salle_id' => $r->getSalleId(),
            'utilisateur_id' => $r->getUtilisateurId(),
            'objet' => $r->getObjet(),
            'date_debut' => $r->getDateDebut(),
            'date_fin' => $r->getDateFin(),
            'statut' => $r->getStatut(),
            'date_creation' => $r->getDateCreation()
        ]);
        return true;
    }

    // Modification / déplacement de réunion : vérifie à nouveau les conflits (hors la réservation elle-même)
    public function updateReservation(Reservation $r)
    {
        if ($this->hasConflict($r->getSalleId(), $r->getDateDebut(), $r->getDateFin(), $r->getId())) {
            return "Conflit : le nouveau créneau chevauche une autre réservation.";
        }

        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE reservation SET salle_id=:salle_id, objet=:objet,
                                date_debut=:date_debut, date_fin=:date_fin WHERE id=:id");
        $stmt->execute([
            'salle_id' => $r->getSalleId(),
            'objet' => $r->getObjet(),
            'date_debut' => $r->getDateDebut(),
            'date_fin' => $r->getDateFin(),
            'id' => $r->getId()
        ]);
        return true;
    }

    public function validerReservation($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'Validée' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $r = $this->getReservationById($id);
        if ($r) {
            Mailer::notifyReservationValidee($r['user_email'], $r['salle_nom'], $r['date_debut'], $r['date_fin']);
        }
    }

    public function refuserReservation($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'Refusée' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $r = $this->getReservationById($id);
        if ($r) {
            Mailer::notifyReservationRefusee($r['user_email'], $r['salle_nom'], $r['date_debut'], $r['date_fin']);
        }
    }

    // Annulation par l'utilisateur (avant date limite - vérifié côté vue) ou par le gestionnaire
    public function annulerReservation($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'Annulée' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $r = $this->getReservationById($id);
        if ($r) {
            Mailer::notifyReservationAnnulee($r['user_email'], $r['salle_nom'], $r['date_debut'], $r['date_fin']);
        }
    }

    public function deleteReservation($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM reservation WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // Recherche multicritère (gestionnaire) : salle, statut, période, utilisateur
    public function rechercheMulticritere($salleId = null, $statut = null, $dateDebut = null, $dateFin = null, $utilisateurEmail = null)
    {
        $pdo = config::getConnexion();
        $sql = "SELECT r.*, s.nom AS salle_nom, b.nom AS batiment_nom,
                       u.nom AS user_nom, u.prenom AS user_prenom, u.email AS user_email
                FROM reservation r
                JOIN salle s ON r.salle_id = s.id
                JOIN batiment b ON s.batiment_id = b.id
                JOIN utilisateur u ON r.utilisateur_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($salleId)) { $sql .= " AND r.salle_id = :salle_id"; $params['salle_id'] = $salleId; }
        if (!empty($statut)) { $sql .= " AND r.statut = :statut"; $params['statut'] = $statut; }
        if (!empty($dateDebut)) { $sql .= " AND r.date_debut >= :date_debut"; $params['date_debut'] = $dateDebut; }
        if (!empty($dateFin)) { $sql .= " AND r.date_fin <= :date_fin"; $params['date_fin'] = $dateFin; }
        if (!empty($utilisateurEmail)) { $sql .= " AND u.email LIKE :email"; $params['email'] = '%' . $utilisateurEmail . '%'; }

        $sql .= " ORDER BY r.date_debut DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Rapport de réservations par période (pour l'Admin Bâtiments)
    public function rapportParPeriode($dateDebut, $dateFin)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT r.*, s.nom AS salle_nom, b.nom AS batiment_nom
                                FROM reservation r
                                JOIN salle s ON r.salle_id = s.id
                                JOIN batiment b ON s.batiment_id = b.id
                                WHERE r.date_debut >= :debut AND r.date_fin <= :fin
                                ORDER BY r.date_debut ASC");
        $stmt->execute(['debut' => $dateDebut, 'fin' => $dateFin]);
        return $stmt->fetchAll();
    }

    public function showReservation(Reservation $r)
    {
        $r->show();
    }

    // Détecte les paires de réservations qui se chevauchent sur la même salle
    // (utile pour la vue "Conflits" du Gestionnaire, en cas de saisie manuelle concurrente)
    public function listConflits()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT r1.id AS id1, r2.id AS id2,
                              s.nom AS salle_nom, b.nom AS batiment_nom,
                              r1.objet AS objet1, r1.date_debut AS debut1, r1.date_fin AS fin1,
                              r2.objet AS objet2, r2.date_debut AS debut2, r2.date_fin AS fin2,
                              u1.email AS email1, u2.email AS email2
                              FROM reservation r1
                              JOIN reservation r2 ON r1.salle_id = r2.salle_id AND r1.id < r2.id
                              JOIN salle s ON r1.salle_id = s.id
                              JOIN batiment b ON s.batiment_id = b.id
                              JOIN utilisateur u1 ON r1.utilisateur_id = u1.id
                              JOIN utilisateur u2 ON r2.utilisateur_id = u2.id
                              WHERE r1.statut IN ('En attente','Validée')
                              AND r2.statut IN ('En attente','Validée')
                              AND r1.date_debut < r2.date_fin
                              AND r1.date_fin > r2.date_debut");
        return $stmt->fetchAll();
    }
}
