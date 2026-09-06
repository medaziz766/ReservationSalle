<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/BatimentController.php';
require_once __DIR__ . '/../../Model/Batiment.php';
$controller = new BatimentController();
$active = 'batiments';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $b = new Batiment(
        trim($_POST['nom']),
        trim($_POST['adresse']),
        (int)$_POST['etages'],
        (float)$_POST['latitude'],
        (float)$_POST['longitude']
    );
    $controller->addBatiment($b);
    header("Location: batiments.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Ajouter un bâtiment - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

        <label>Position sur la carte (clique sur la carte pour placer le bâtiment)</label>
        <div id="map" style="height:320px; border-radius:8px; margin-bottom:10px;"></div>

        <label for="latitude">Latitude</label>
        <input type="text" id="latitude" name="latitude" placeholder="Clique sur la carte" readonly>

        <label for="longitude">Longitude</label>
        <input type="text" id="longitude" name="longitude" placeholder="Clique sur la carte" readonly>

        <button type="submit" class="btn">Ajouter</button>
    </form>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Centré par défaut sur El Ghazala, Tunisie
    var defaultLat = 36.89875, defaultLng = 10.18972;
    var map = L.map('map').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = null;

    function placeMarker(lat, lng) {
        if (marker) { map.removeLayer(marker); }
        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup('Position du bâtiment').openPopup();
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });
</script>
<script src="assets/js/validateBatiment.js"></script>
</body>
</html>
