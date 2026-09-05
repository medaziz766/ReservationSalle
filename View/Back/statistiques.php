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

    <h2 style="font-size:16px; color:#374151; margin-bottom:12px;">Utilisation par salle</h2>
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
