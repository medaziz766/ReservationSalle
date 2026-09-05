<?php
require_once __DIR__ . '/../../config.php';
requireRole('Utilisateur');
$active = $active ?? '';
?>
<header class="site-header">
    <div class="logo">Room<span>Booking</span></div>
    <nav class="main-nav">
        <a href="index.php" class="<?= $active==='home'?'active':'' ?>">Accueil</a>
        <a href="salles.php" class="<?= $active==='salles'?'active':'' ?>">Salles</a>
        <a href="mesReservations.php" class="<?= $active==='mesReservations'?'active':'' ?>">Mes réservations</a>
        <a href="../Auth/logout.php">Se déconnecter</a>
    </nav>
</header>
