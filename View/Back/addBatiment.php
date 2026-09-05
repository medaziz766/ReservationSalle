<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/BatimentController.php';
require_once __DIR__ . '/../../Model/Batiment.php';
$controller = new BatimentController();
$active = 'batiments';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $b = new Batiment(trim($_POST['nom']), trim($_POST['adresse']), (int)$_POST['etages']);
    $controller->addBatiment($b);
    header("Location: batiments.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter un bâtiment - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Ajouter un bâtiment</h1>
    <form id="batimentForm" class="admin-form" method="POST" action="addBatiment.php" novalidate>
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" placeholder="Enter building name">

        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" placeholder="Enter address">

        <label for="etages">Nombre d'étages</label>
        <input type="number" id="etages" name="etages" placeholder="Enter number of floors">

        <button type="submit" class="btn">Ajouter</button>
    </form>
</div>
<script src="assets/js/validateBatiment.js"></script>
</body>
</html>
