<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Controller/AuthController.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';
$controller = new AuthController();
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = new Utilisateur(
        trim($_POST['nom']),
        trim($_POST['prenom']),
        trim($_POST['email']),
        $_POST['password'],
        'Utilisateur', // rôle par défaut à l'inscription
        date('Y-m-d')
    );
    $result = $controller->register($u);
    if ($result === true) {
        $success = true;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Créer un compte - RoomBooking</title>
<link rel="stylesheet" href="../Front/assets/css/style.css">
</head>
<body>
<section style="max-width:480px; margin:60px auto;">
    <h2 style="text-align:center; border:none;">Créer un compte</h2>

    <?php if ($success): ?>
        <p class="msg-success" style="text-align:center;">Compte créé avec succès !</p>
        <p style="text-align:center;"><a class="btn" href="login.php">Se connecter</a></p>
    <?php else: ?>
        <?php if ($error): ?><p class="msg-error" style="text-align:center;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form id="registerForm" class="user-form" method="POST" action="register.php" novalidate>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Enter your last name">

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" placeholder="Enter your first name">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">

            <button type="submit" class="btn">Créer mon compte</button>
        </form>
        <p style="text-align:center; margin-top:16px; color:var(--text-dim); font-size:13px;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    <?php endif; ?>
</section>
<script src="assets/js/validateAuth.js"></script>
</body>
</html>
