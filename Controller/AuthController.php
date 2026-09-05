<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Utilisateur.php';

class AuthController
{
    public function register(Utilisateur $u)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $u->getEmail()]);
        if ($stmt->fetch()) {
            return "Un compte existe déjà avec cet email.";
        }

        $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, password, role, date_creation)
                                VALUES (:nom, :prenom, :email, :password, :role, :date_creation)");
        $stmt->execute([
            'nom' => $u->getNom(),
            'prenom' => $u->getPrenom(),
            'email' => $u->getEmail(),
            'password' => password_hash($u->getPassword(), PASSWORD_DEFAULT),
            'role' => $u->getRole(),
            'date_creation' => $u->getDateCreation()
        ]);
        return true;
    }

    public function login($email, $password)
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if (!$data || !password_verify($password, $data['password'])) {
            return "Email ou mot de passe incorrect.";
        }

        $_SESSION['user_id'] = $data['id'];
        $_SESSION['nom'] = $data['nom'];
        $_SESSION['prenom'] = $data['prenom'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = $data['role'];
        return true;
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
    }
}
