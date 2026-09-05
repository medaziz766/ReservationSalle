<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/BatimentController.php';
require_once __DIR__ . '/../../Model/Batiment.php';
$controller = new BatimentController();
$active = 'batiments';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: batiments.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $b = new Batiment(trim($_POST['nom']), trim($_POST['adresse']), (int)$_POST['etages'], $_POST['id']);
    $controller->updateBatiment($b);
    header("Location: batiments.php");
    exit;
}

$data = $controller->getBatimentById($id);
if (!$data) { header("Location: batiments.php"); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier un bâtiment - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Modifier le bâtiment</h1>
    <form id="batimentForm" class="admin-form" method="POST" action="updateBatiment.php?id=<?= $data['id'] ?>" novalidate>
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($data['nom']) ?>">

        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($data['adresse']) ?>">

        <label for="etages">Nombre d'étages</label>
        <input type="number" id="etages" name="etages" value="<?= (int)$data['nombre_etages'] ?>">

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>
<script src="assets/js/validateBatiment.js"></script>
</body>
</html>
