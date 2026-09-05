<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/SalleController.php';
$controller = new SalleController();

if (isset($_GET['set']) && isset($_GET['id'])) {
    $controller->updateStatut($_GET['id'], $_GET['set']);
    header("Location: maintenance.php");
    exit;
}

$salles = $controller->listSalles();
$active = 'maintenance';

function statutBadge($statut) {
    $map = ['Disponible' => 'badge-success', 'Maintenance' => 'badge-warning', 'Indisponible' => 'badge-danger'];
    $cls = $map[$statut] ?? 'badge-info';
    return "<span class='badge $cls'>" . htmlspecialchars($statut) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Maintenance - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Maintenance &amp; Disponibilité</h1>
    <p class="subtitle">Changer rapidement le statut d'une salle (mise en maintenance, remise en service...).</p>

    <table class="admin-table">
        <tr><th>Salle</th><th>Bâtiment</th><th>Statut actuel</th><th>Actions</th></tr>
        <?php foreach ($salles as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['nom']) ?></td>
            <td><?= htmlspecialchars($s['batiment_nom']) ?></td>
            <td><?= statutBadge($s['statut']) ?></td>
            <td class="actions">
                <a href="maintenance.php?id=<?= $s['id'] ?>&set=Disponible" class="validate">Disponible</a>
                <a href="maintenance.php?id=<?= $s['id'] ?>&set=Maintenance" class="update">Maintenance</a>
                <a href="maintenance.php?id=<?= $s['id'] ?>&set=Indisponible" class="refuse">Indisponible</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
