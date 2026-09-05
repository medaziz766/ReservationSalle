<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/SalleController.php';
$controller = new SalleController();

if (isset($_GET['delete'])) {
    $controller->deleteSalle($_GET['delete']);
    header("Location: salles.php");
    exit;
}

$salles = $controller->listSalles();
$active = 'salles';

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
<title>Salles - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Salles</h1>
    <p class="subtitle">Ajouter et configurer les salles (capacité, équipements, localisation).</p>
    <a href="addSalle.php" class="btn-add">+ Ajouter une salle</a>

    <table class="admin-table">
        <tr><th>Nom</th><th>Bâtiment</th><th>Étage</th><th>Capacité</th><th>Équipements</th><th>Statut</th><th>Actions</th></tr>
        <?php foreach ($salles as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['nom']) ?></td>
            <td><?= htmlspecialchars($s['batiment_nom']) ?></td>
            <td><?= (int)$s['etage'] ?></td>
            <td><?= (int)$s['capacite'] ?></td>
            <td><?= htmlspecialchars($s['equipements']) ?></td>
            <td><?= statutBadge($s['statut']) ?></td>
            <td class="actions">
                <a href="updateSalle.php?id=<?= $s['id'] ?>" class="update">modifier</a>
                <a href="salles.php?delete=<?= $s['id'] ?>" class="delete" onclick="return confirm('Supprimer cette salle ?');">supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
