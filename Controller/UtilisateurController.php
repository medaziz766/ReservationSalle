<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Utilisateur.php';

class UtilisateurController
{
    public function listUtilisateurs()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getUtilisateurById($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function updateRole($id, $role)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE utilisateur SET role = :role WHERE id = :id");
        $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public function deleteUtilisateur($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function showUtilisateur(Utilisateur $u)
    {
        $u->show();
    }
}
