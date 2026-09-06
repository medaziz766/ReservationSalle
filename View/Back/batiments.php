<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/BatimentController.php';
$controller = new BatimentController();

if (isset($_GET['delete'])) {
    $controller->deleteBatiment($_GET['delete']);
    header("Location: batiments.php");
    exit;
}

$recherche = $_GET['recherche'] ?? '';
$batiments = $controller->searchBatiments($recherche);
$active = 'batiments';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Bâtiments - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Bâtiments</h1>
    <p class="subtitle">Créer et gérer les bâtiments et leurs étages.</p>
    <a href="addBatiment.php" class="btn-add">+ Ajouter un bâtiment</a>

    <form class="filter-bar" method="GET" action="batiments.php">
        <input type="text" name="recherche" placeholder="Rechercher par nom ou adresse..." value="<?= htmlspecialchars($recherche) ?>">
        <button type="submit">Rechercher</button>
        <?php if ($recherche): ?><a href="batiments.php" class="btn-outline" style="text-decoration:none; display:inline-flex; align-items:center;">Réinitialiser</a><?php endif; ?>
    </form>

    <table class="admin-table">
        <tr><th>Nom</th><th>Adresse</th><th>Étages</th><th>Actions</th></tr>
        <?php foreach ($batiments as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['nom']) ?></td>
            <td><?= htmlspecialchars($b['adresse']) ?></td>
            <td><?= (int)$b['nombre_etages'] ?></td>
            <td class="actions">
                <a href="updateBatiment.php?id=<?= $b['id'] ?>" class="update">modifier</a>
                <a href="batiments.php?delete=<?= $b['id'] ?>" class="delete" onclick="return confirm('Supprimer ce bâtiment ?');">supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($batiments)): ?>
        <tr><td colspan="4">Aucun bâtiment trouvé.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
