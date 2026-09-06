<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/BatimentController.php';
$controller = new SalleController();
$batimentController = new BatimentController();

if (isset($_GET['delete'])) {
    $controller->deleteSalle($_GET['delete']);
    header("Location: salles.php");
    exit;
}

$recherche = $_GET['recherche'] ?? '';
$batimentId = $_GET['batimentId'] ?? '';
$statut = $_GET['statut'] ?? '';

$salles = $controller->filterSallesAdmin($recherche ?: null, $batimentId ?: null, $statut ?: null);
$batiments = $batimentController->listBatiments();
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

    <form class="filter-bar" method="GET" action="salles.php">
        <input type="text" name="recherche" placeholder="Rechercher (nom, équipement)..." value="<?= htmlspecialchars($recherche) ?>">
        <select name="batimentId">
            <option value="">Tous les bâtiments</option>
            <?php foreach ($batiments as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $batimentId == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="statut">
            <option value="">Tous les statuts</option>
            <?php foreach (['Disponible','Maintenance','Indisponible'] as $st): ?>
            <option value="<?= $st ?>" <?= $statut === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
        <?php if ($recherche || $batimentId || $statut): ?><a href="salles.php" class="btn-outline" style="text-decoration:none; display:inline-flex; align-items:center;">Réinitialiser</a><?php endif; ?>
    </form>

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
        <?php if (empty($salles)): ?>
        <tr><td colspan="7">Aucune salle trouvée pour ces critères.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
