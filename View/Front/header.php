<?php
require_once __DIR__ . '/../../config.php';
requireRole('Utilisateur');
require_once __DIR__ . '/../../Controller/NotificationController.php';
$active = $active ?? '';
$nbNonLues = (new NotificationController())->compterNonLuesUtilisateur($_SESSION['user_id']);
?>
<header class="site-header">
    <div class="logo">Room<span>Booking</span></div>
    <nav class="main-nav">
        <a href="index.php" class="<?= $active==='home'?'active':'' ?>">Accueil</a>
        <a href="salles.php" class="<?= $active==='salles'?'active':'' ?>">Salles</a>
        <a href="mesReservations.php" class="<?= $active==='mesReservations'?'active':'' ?>">Mes réservations</a>
        <a href="boiteMail.php" class="<?= $active==='boiteMail'?'active':'' ?>">
            Boîte mail<?php if ($nbNonLues > 0): ?> <span class="nav-badge"><?= $nbNonLues ?></span><?php endif; ?>
        </a>
        <button id="themeToggle" class="theme-toggle" type="button" title="Changer de thème">🌙</button>
        <a href="../Auth/logout.php">Se déconnecter</a>
    </nav>
</header>
<script src="assets/js/theme.js"></script>
