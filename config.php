<?php
session_start();

class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "reservation_system";

            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                die('Erreur de connexion : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Petites fonctions utilitaires de session / droits d'accès
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function currentRole()
{
    return $_SESSION['role'] ?? null;
}

function requireRole($roles)
{
    if (!isLoggedIn() || !in_array(currentRole(), (array)$roles)) {
        header("Location: ../Auth/login.php");
        exit;
    }
}
