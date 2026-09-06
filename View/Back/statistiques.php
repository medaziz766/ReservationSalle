<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/ReservationController.php';
$salleController = new SalleController();
$reservationController = new ReservationController();

$stats = $salleController->statistiquesUtilisation();
$allReservations = $reservationController->listReservations();

$totalReservations = count($allReservations);
$totalValidees = count(array_filter($allReservations, fn($r) => $r['statut'] === 'Validée'));
$totalEnAttente = count(array_filter($allReservations, fn($r) => $r['statut'] === 'En attente'));
$totalRefusees = count(array_filter($allReservations, fn($r) => $r['statut'] === 'Refusée'));

$active = 'statistiques';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Statistiques - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Statistiques d'utilisation</h1>
    <p class="subtitle">Vue d'ensemble de l'activité de réservation.</p>

    <div class="stats-grid">
        <div class="stat-card"><div class="num"><?= $totalReservations ?></div><div class="label">Réservations totales</div></div>
        <div class="stat-card"><div class="num"><?= $totalValidees ?></div><div class="label">Validées</div></div>
        <div class="stat-card"><div class="num"><?= $totalEnAttente ?></div><div class="label">En attente</div></div>
        <div class="stat-card"><div class="num"><?= $totalRefusees ?></div><div class="label">Refusées</div></div>
    </div>

    <h2 style="font-size:16px; margin-bottom:12px;">Taux d'utilisation par salle</h2>
    <p class="subtitle" style="margin-top:-8px;">Part de chaque salle dans le total des réservations validées.</p>
    <div class="progress-grid">
        <?php foreach ($stats as $s):
            $pct = $totalValidees > 0 ? round(((int)$s['validees'] / $totalValidees) * 100) : 0;
            $niveau = $pct >= 40 ? 'high' : ($pct >= 15 ? 'mid' : 'low');
        ?>
        <div class="progress-card">
            <svg class="progress-circle" viewBox="0 0 36 36">
                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="circle <?= $niveau ?>" stroke-dasharray="<?= $pct ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <text x="18" y="20.5" class="pct-text"><?= $pct ?>%</text>
            </svg>
            <div class="room-name"><?= htmlspecialchars($s['nom']) ?></div>
            <div class="room-sub"><?= htmlspecialchars($s['batiment_nom']) ?> — <?= (int)$s['validees'] ?> validée(s)</div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($stats)): ?>
        <p>Aucune salle enregistrée pour le moment.</p>
        <?php endif; ?>
    </div>

    <h2 style="font-size:16px; margin-bottom:12px;">Détail par salle</h2>
    <table class="admin-table">
        <tr><th>Salle</th><th>Bâtiment</th><th>Total réservations</th><th>Validées</th></tr>
        <?php foreach ($stats as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['nom']) ?></td>
            <td><?= htmlspecialchars($s['batiment_nom']) ?></td>
            <td><?= (int)$s['total_reservations'] ?></td>
            <td><?= (int)$s['validees'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
