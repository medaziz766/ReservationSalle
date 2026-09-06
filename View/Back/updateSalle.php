<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/BatimentController.php';
require_once __DIR__ . '/../../Model/Salle.php';
$controller = new SalleController();
$batimentController = new BatimentController();
$batiments = $batimentController->listBatiments();
$active = 'salles';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: salles.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = new Salle(
        (int)$_POST['batimentId'],
        (int)$_POST['etage'],
        trim($_POST['nom']),
        (int)$_POST['capacite'],
        trim($_POST['equipements']),
        $_POST['statut'],
        $_POST['id']
    );
    $controller->updateSalle($s);
    header("Location: salles.php");
    exit;
}

$data = $controller->getSalleById($id);
if (!$data) { header("Location: salles.php"); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Modifier une salle - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Modifier la salle</h1>
    <form id="salleForm" class="admin-form" method="POST" action="updateSalle.php?id=<?= $data['id'] ?>" novalidate>
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label for="batimentId">Bâtiment</label>
        <select id="batimentId" name="batimentId">
            <?php foreach ($batiments as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $data['batiment_id'] == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="etage">Étage</label>
        <input type="number" id="etage" name="etage" value="<?= (int)$data['etage'] ?>">

        <label for="nom">Nom de la salle</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($data['nom']) ?>">

        <label for="capacite">Capacité</label>
        <input type="number" id="capacite" name="capacite" value="<?= (int)$data['capacite'] ?>">

        <label for="equipements">Équipements</label>
        <input type="text" id="equipements" name="equipements" value="<?= htmlspecialchars($data['equipements']) ?>">

        <label for="statut">Statut</label>
        <select id="statut" name="statut">
            <?php foreach (['Disponible','Maintenance','Indisponible'] as $st): ?>
            <option value="<?= $st ?>" <?= $data['statut'] === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>
<script src="assets/js/validateSalle.js"></script>
</body>
</html>
