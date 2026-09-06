<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Batiment.php';

class BatimentController
{
    public function listBatiments()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM batiment ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function searchBatiments($motCle = null)
    {
        $pdo = config::getConnexion();
        if (empty($motCle)) {
            return $this->listBatiments();
        }
        $stmt = $pdo->prepare("SELECT * FROM batiment WHERE nom LIKE :mot OR adresse LIKE :mot ORDER BY id DESC");
        $stmt->execute(['mot' => '%' . $motCle . '%']);
        return $stmt->fetchAll();
    }

    public function getBatimentById($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM batiment WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function addBatiment(Batiment $b)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO batiment (nom, adresse, nombre_etages) VALUES (:nom, :adresse, :etages)");
        $stmt->execute([
            'nom' => $b->getNom(),
            'adresse' => $b->getAdresse(),
            'etages' => $b->getNombreEtages()
        ]);
    }

    public function updateBatiment(Batiment $b)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE batiment SET nom=:nom, adresse=:adresse, nombre_etages=:etages WHERE id=:id");
        $stmt->execute([
            'nom' => $b->getNom(),
            'adresse' => $b->getAdresse(),
            'etages' => $b->getNombreEtages(),
            'id' => $b->getId()
        ]);
    }

    public function deleteBatiment($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM batiment WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function showBatiment(Batiment $b)
    {
        $b->show();
    }
}
