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
    $b = new Batiment(
        trim($_POST['nom']),
        trim($_POST['adresse']),
        (int)$_POST['etages'],
        (float)$_POST['latitude'],
        (float)$_POST['longitude'],
        $_POST['id']
    );
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
<script src="assets/js/theme.js"></script>
<title>Modifier un bâtiment - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

        <label>Position sur la carte (clique pour repositionner)</label>
        <div id="map" style="height:320px; border-radius:8px; margin-bottom:10px;"></div>

        <label for="latitude">Latitude</label>
        <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>" readonly>

        <label for="longitude">Longitude</label>
        <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>" readonly>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var lat = <?= (float)$data['latitude'] ?>, lng = <?= (float)$data['longitude'] ?>;
    var map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng]).addTo(map).bindPopup('Position actuelle').openPopup();

    map.on('click', function (e) {
        map.removeLayer(marker);
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map)
            .bindPopup('Nouvelle position').openPopup();
        document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
    });
</script>
<script src="assets/js/validateBatiment.js"></script>
</body>
</html>
