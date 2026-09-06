<?php
require_once __DIR__ . '/../../Controller/BatimentController.php';
$controller = new BatimentController();
$batiments = $controller->listBatiments();
$active = 'carte';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Carte des bâtiments - RoomBooking</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #batimentsMap{ height:520px; border-radius:10px; border:1px solid var(--line); }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Nos bâtiments</h2>
    <p style="color:var(--text-dim);">Clique sur un marqueur pour voir les infos du bâtiment et accéder à ses salles.</p>
    <div id="batimentsMap"></div>
</section>

<?php include 'footer.php'; ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var batiments = <?= json_encode(array_map(function ($b) {
        return [
            'id' => $b['id'],
            'nom' => $b['nom'],
            'adresse' => $b['adresse'],
            'etages' => (int)$b['nombre_etages'],
            'lat' => (float)$b['latitude'],
            'lng' => (float)$b['longitude'],
        ];
    }, $batiments)) ?>;

    var defaultLat = 36.89875, defaultLng = 10.18972;
    var map = L.map('batimentsMap').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var bounds = [];
    batiments.forEach(function (b) {
        var popupHtml = '<strong>' + b.nom + '</strong><br>' + b.adresse +
            '<br>' + b.etages + ' étage(s)' +
            '<br><a href="salles.php?batimentId=' + b.id + '">Voir les salles →</a>';
        L.marker([b.lat, b.lng]).addTo(map).bindPopup(popupHtml);
        bounds.push([b.lat, b.lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    } else if (bounds.length === 1) {
        map.setView(bounds[0], 16);
    }
</script>
</body>
</html>
