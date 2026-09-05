<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Controller/AuthController.php';
$controller = new AuthController();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->login(trim($_POST['email']), $_POST['password']);
    if ($result === true) {
        switch ($_SESSION['role']) {
            case 'Admin': header("Location: ../Back/batiments.php"); break;
            case 'Gestionnaire': header("Location: ../Back/demandes.php"); break;
            default: header("Location: ../Front/index.php"); break;
        }
        exit;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion - RoomBooking</title>
<link rel="stylesheet" href="../Front/assets/css/style.css">
</head>
<body>
<section style="max-width:480px; margin:60px auto;">
    <h2 style="text-align:center; border:none;">Connexion</h2>
    <?php if ($error): ?><p class="msg-error" style="text-align:center;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form id="loginForm" class="user-form" method="POST" action="login.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Enter your password">

        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p style="text-align:center; margin-top:16px; color:var(--text-dim); font-size:13px;">
        Pas de compte ? <a href="register.php">Créer un compte</a>
    </p>
</section>
<script src="assets/js/validateAuth.js"></script>
</body>
</html>
