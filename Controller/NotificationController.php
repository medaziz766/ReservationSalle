<?php
require_once __DIR__ . '/../config.php';

class NotificationController
{
    // Crée une notification interne. $destinataireRole indique dans quelle boîte elle apparaît :
    // 'Utilisateur' -> visible par le demandeur concerné (utilisateurId)
    // 'Gestionnaire' -> visible par tous les gestionnaires (boîte commune)
    public function creer($utilisateurId, $reservationId, $destinataireRole, $type, $message)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO notification (utilisateur_id, reservation_id, destinataire_role, type, message, date_creation)
                                VALUES (:utilisateur_id, :reservation_id, :destinataire_role, :type, :message, :date_creation)");
        $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'reservation_id' => $reservationId,
            'destinataire_role' => $destinataireRole,
            'type' => $type,
            'message' => $message,
            'date_creation' => date('Y-m-d H:i:s')
        ]);
    }

    // Boîte de l'utilisateur connecté (ses propres échanges avec le gestionnaire)
    public function listPourUtilisateur($utilisateurId)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT n.*, r.objet AS reservation_objet
                                FROM notification n
                                LEFT JOIN reservation r ON n.reservation_id = r.id
                                WHERE n.destinataire_role = 'Utilisateur' AND n.utilisateur_id = :uid
                                ORDER BY n.date_creation DESC");
        $stmt->execute(['uid' => $utilisateurId]);
        return $stmt->fetchAll();
    }

    // Boîte commune des gestionnaires : toutes les demandes/réponses des utilisateurs
    public function listPourGestionnaire()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT n.*, r.objet AS reservation_objet,
                              u.nom AS user_nom, u.prenom AS user_prenom
                              FROM notification n
                              LEFT JOIN reservation r ON n.reservation_id = r.id
                              JOIN utilisateur u ON n.utilisateur_id = u.id
                              WHERE n.destinataire_role = 'Gestionnaire'
                              ORDER BY n.date_creation DESC");
        return $stmt->fetchAll();
    }

    public function compterNonLuesUtilisateur($utilisateurId)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notification
                                WHERE destinataire_role = 'Utilisateur' AND utilisateur_id = :uid AND lu = 0");
        $stmt->execute(['uid' => $utilisateurId]);
        return (int)$stmt->fetch()['total'];
    }

    public function compterNonLuesGestionnaire()
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM notification
                              WHERE destinataire_role = 'Gestionnaire' AND lu = 0");
        return (int)$stmt->fetch()['total'];
    }

    public function marquerCommeLue($id)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE notification SET lu = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function marquerToutesLuesUtilisateur($utilisateurId)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE notification SET lu = 1 WHERE destinataire_role = 'Utilisateur' AND utilisateur_id = :uid");
        $stmt->execute(['uid' => $utilisateurId]);
    }

    public function marquerToutesLuesGestionnaire()
    {
        $pdo = config::getConnexion();
        $pdo->exec("UPDATE notification SET lu = 1 WHERE destinataire_role = 'Gestionnaire'");
    }
}
