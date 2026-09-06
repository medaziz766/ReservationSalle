<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Salle.php';

class SalleController
{
    // Jointure avec batiment pour afficher le nom du bâtiment
    public function listSalles()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT s.*, b.nom AS batiment_nom
                              FROM salle s
                              JOIN batiment b ON s.batiment_id = b.id
                              ORDER BY s.id DESC");
        return $stmt->fetchAll();
    }

    public function getSalleById($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT s.*, b.nom AS batiment_nom
                                FROM salle s
                                JOIN batiment b ON s.batiment_id = b.id
                                WHERE s.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function addSalle(Salle $s)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO salle (batiment_id, etage, nom, capacite, equipements, statut)
                                VALUES (:batiment_id, :etage, :nom, :capacite, :equipements, :statut)");
        $stmt->execute([
            'batiment_id' => $s->getBatimentId(),
            'etage' => $s->getEtage(),
            'nom' => $s->getNom(),
            'capacite' => $s->getCapacite(),
            'equipements' => $s->getEquipements(),
            'statut' => $s->getStatut()
        ]);
    }

    public function updateSalle(Salle $s)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE salle SET batiment_id=:batiment_id, etage=:etage, nom=:nom,
                                capacite=:capacite, equipements=:equipements, statut=:statut WHERE id=:id");
        $stmt->execute([
            'batiment_id' => $s->getBatimentId(),
            'etage' => $s->getEtage(),
            'nom' => $s->getNom(),
            'capacite' => $s->getCapacite(),
            'equipements' => $s->getEquipements(),
            'statut' => $s->getStatut(),
            'id' => $s->getId()
        ]);
    }

    public function updateStatut($id, $statut)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE salle SET statut = :statut WHERE id = :id");
        $stmt->execute(['statut' => $statut, 'id' => $id]);
    }

    public function deleteSalle($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM salle WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function showSalle(Salle $s)
    {
        $s->show();
    }

    // Recherche multicritère de salles disponibles (front office)
    public function searchSalles($capaciteMin = null, $batimentId = null, $equipementMotCle = null)
    {
        $pdo = config::getConnexion();
        $sql = "SELECT s.*, b.nom AS batiment_nom
                FROM salle s JOIN batiment b ON s.batiment_id = b.id
                WHERE s.statut = 'Disponible'";
        $params = [];

        if (!empty($capaciteMin)) {
            $sql .= " AND s.capacite >= :capacite";
            $params['capacite'] = $capaciteMin;
        }
        if (!empty($batimentId)) {
            $sql .= " AND s.batiment_id = :batiment_id";
            $params['batiment_id'] = $batimentId;
        }
        if (!empty($equipementMotCle)) {
            $sql .= " AND s.equipements LIKE :equipement";
            $params['equipement'] = '%' . $equipementMotCle . '%';
        }
        $sql .= " ORDER BY s.capacite ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Statistiques d'utilisation : nombre de réservations validées par salle
    public function statistiquesUtilisation()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT s.nom, b.nom AS batiment_nom,
                              COUNT(r.id) AS total_reservations,
                              SUM(CASE WHEN r.statut = 'Validée' THEN 1 ELSE 0 END) AS validees
                              FROM salle s
                              JOIN batiment b ON s.batiment_id = b.id
                              LEFT JOIN reservation r ON r.salle_id = s.id
                              GROUP BY s.id
                              ORDER BY total_reservations DESC");
        return $stmt->fetchAll();
    }

    // Filtre pour la liste Admin (contrairement à searchSalles, n'impose pas statut='Disponible')
    public function filterSallesAdmin($motCle = null, $batimentId = null, $statut = null)
    {
        $pdo = config::getConnexion();
        $sql = "SELECT s.*, b.nom AS batiment_nom
                FROM salle s JOIN batiment b ON s.batiment_id = b.id
                WHERE 1=1";
        $params = [];

        if (!empty($motCle)) {
            $sql .= " AND (s.nom LIKE :mot OR s.equipements LIKE :mot)";
            $params['mot'] = '%' . $motCle . '%';
        }
        if (!empty($batimentId)) {
            $sql .= " AND s.batiment_id = :batiment_id";
            $params['batiment_id'] = $batimentId;
        }
        if (!empty($statut)) {
            $sql .= " AND s.statut = :statut";
            $params['statut'] = $statut;
        }
        $sql .= " ORDER BY s.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
