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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = new Salle(
        (int)$_POST['batimentId'],
        (int)$_POST['etage'],
        trim($_POST['nom']),
        (int)$_POST['capacite'],
        trim($_POST['equipements']),
        $_POST['statut']
    );
    $controller->addSalle($s);
    header("Location: salles.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter une salle - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Ajouter une salle</h1>
    <form id="salleForm" class="admin-form" method="POST" action="addSalle.php" novalidate>
        <label for="batimentId">Bâtiment</label>
        <select id="batimentId" name="batimentId">
            <option value="">-- choisir --</option>
            <?php foreach ($batiments as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="etage">Étage</label>
        <input type="number" id="etage" name="etage" placeholder="Enter floor number">

        <label for="nom">Nom de la salle</label>
        <input type="text" id="nom" name="nom" placeholder="Enter room name">

        <label for="capacite">Capacité</label>
        <input type="number" id="capacite" name="capacite" placeholder="Enter capacity">

        <label for="equipements">Équipements</label>
        <input type="text" id="equipements" name="equipements" placeholder="Ex: Projecteur, Visioconférence">

        <label for="statut">Statut</label>
        <select id="statut" name="statut">
            <option value="Disponible">Disponible</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Indisponible">Indisponible</option>
        </select>

        <button type="submit" class="btn">Ajouter</button>
    </form>
</div>
<script src="assets/js/validateSalle.js"></script>
</body>
</html>
