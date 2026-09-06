<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/ReservationController.php';
$controller = new ReservationController();

$dateDebut = $_GET['dateDebut'] ?? date('Y-m-01');
$dateFin = $_GET['dateFin'] ?? date('Y-m-t');
$reservations = $controller->rapportParPeriode($dateDebut . ' 00:00:00', $dateFin . ' 23:59:59');

$active = 'rapports';

function statutBadgeR($statut) {
    $map = ['Validée' => 'badge-success', 'En attente' => 'badge-warning', 'Refusée' => 'badge-danger', 'Annulée' => 'badge-info'];
    $cls = $map[$statut] ?? 'badge-info';
    return "<span class='badge $cls'>" . htmlspecialchars($statut) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapports - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Rapports de réservation</h1>
    <p class="subtitle">Générer un rapport des réservations sur une période donnée.</p>

    <form class="filter-bar" method="GET" action="rapports.php">
        <div>
            <label style="font-size:11px; color:var(--text-dim); display:block;">Du</label>
            <input type="date" name="dateDebut" value="<?= htmlspecialchars($dateDebut) ?>">
        </div>
        <div>
            <label style="font-size:11px; color:var(--text-dim); display:block;">Au</label>
            <input type="date" name="dateFin" value="<?= htmlspecialchars($dateFin) ?>">
        </div>
        <button type="submit" style="align-self:flex-end;">Filtrer</button>
    </form>

    <p class="subtitle"><?= count($reservations) ?> réservation(s) trouvée(s) entre le <?= htmlspecialchars($dateDebut) ?> et le <?= htmlspecialchars($dateFin) ?>.</p>

    <a class="btn-add" href="exportRapportPDF.php?dateDebut=<?= htmlspecialchars($dateDebut) ?>&dateFin=<?= htmlspecialchars($dateFin) ?>" target="_blank">⬇ Exporter en PDF</a>

    <table class="admin-table">
        <tr><th>Salle</th><th>Bâtiment</th><th>Début</th><th>Fin</th><th>Statut</th></tr>
        <?php foreach ($reservations as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['salle_nom']) ?></td>
            <td><?= htmlspecialchars($r['batiment_nom']) ?></td>
            <td><?= htmlspecialchars($r['date_debut']) ?></td>
            <td><?= htmlspecialchars($r['date_fin']) ?></td>
            <td><?= statutBadgeR($r['statut']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($reservations)): ?>
        <tr><td colspan="5">Aucune réservation sur cette période.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
