<?php
require_once __DIR__ . '/../../config.php';
requireRole(['Admin', 'Gestionnaire']);
require_once __DIR__ . '/../../Controller/NotificationController.php';
$active = $active ?? '';
$role = $_SESSION['role'];
$nbNonLues = $role === 'Gestionnaire' ? (new NotificationController())->compterNonLuesGestionnaire() : 0;
?>
<div class="sidebar">
    <div class="brand">Room<span>Booking</span></div>
    <div class="role-tag"><?= htmlspecialchars($role) ?> — <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?></div>
    <nav>
        <?php if ($role === 'Admin'): ?>
            <a href="batiments.php" class="<?= $active === 'batiments' ? 'active' : '' ?>">Bâtiments</a>
            <a href="salles.php" class="<?= $active === 'salles' ? 'active' : '' ?>">Salles</a>
            <a href="maintenance.php" class="<?= $active === 'maintenance' ? 'active' : '' ?>">Maintenance</a>
            <a href="statistiques.php" class="<?= $active === 'statistiques' ? 'active' : '' ?>">Statistiques</a>
            <a href="rapports.php" class="<?= $active === 'rapports' ? 'active' : '' ?>">Rapports</a>
        <?php elseif ($role === 'Gestionnaire'): ?>
            <a href="demandes.php" class="<?= $active === 'demandes' ? 'active' : '' ?>">Demandes</a>
            <a href="reservationManuelle.php" class="<?= $active === 'manuelle' ? 'active' : '' ?>">Réservation manuelle</a>
            <a href="conflits.php" class="<?= $active === 'conflits' ? 'active' : '' ?>">Conflits</a>
            <a href="recherche.php" class="<?= $active === 'recherche' ? 'active' : '' ?>">Recherche</a>
            <a href="boiteMail.php" class="<?= $active === 'boiteMail' ? 'active' : '' ?>">
                Boîte mail<?php if ($nbNonLues > 0): ?> <span class="nav-badge"><?= $nbNonLues ?></span><?php endif; ?>
            </a>
        <?php endif; ?>
    </nav>
    <button id="themeToggle" class="theme-toggle" type="button" title="Changer de thème">🌙</button>
    <a href="../Auth/logout.php" class="logout">Se déconnecter</a>
</div>
