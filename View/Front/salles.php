<?php
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/BatimentController.php';
$salleController = new SalleController();
$batimentController = new BatimentController();
$batiments = $batimentController->listBatiments();
$active = 'salles';

$capacite = $_GET['capacite'] ?? '';
$batimentId = $_GET['batimentId'] ?? '';
$equipement = $_GET['equipement'] ?? '';

$salles = $salleController->searchSalles($capacite ?: null, $batimentId ?: null, $equipement ?: null);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Salles disponibles - RoomBooking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Salles disponibles</h2>

    <form class="filter-bar" method="GET" action="salles.php">
        <input type="number" name="capacite" min="1" placeholder="Capacité min." value="<?= htmlspecialchars($capacite) ?>">
        <select name="batimentId">
            <option value="">Tous les bâtiments</option>
            <?php foreach ($batiments as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $batimentId == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="equipement" placeholder="Équipement (ex: Projecteur)" value="<?= htmlspecialchars($equipement) ?>">
        <button type="submit">Filtrer</button>
    </form>

    <div class="grid">
        <?php foreach ($salles as $s): ?>
        <div class="card">
            <span class="badge badge-success">Disponible</span>
            <h3><?= htmlspecialchars($s['nom']) ?></h3>
            <p><?= htmlspecialchars($s['batiment_nom']) ?> — Étage <?= (int)$s['etage'] ?></p>
            <p style="color:var(--text-dim); font-size:13px;">Capacité : <?= (int)$s['capacite'] ?> personnes</p>
            <p style="color:var(--text-dim); font-size:13px;"><?= htmlspecialchars($s['equipements']) ?></p>
            <a class="btn" href="calendrier.php?salleId=<?= $s['id'] ?>">Voir le calendrier</a>
            <button type="button" class="btn-map" onclick="openMapModal('<?= htmlspecialchars($s['batiment_nom'], ENT_QUOTES) ?>', '<?= htmlspecialchars($s['batiment_adresse'], ENT_QUOTES) ?>', <?= (float)$s['batiment_lat'] ?>, <?= (float)$s['batiment_lng'] ?>)">📍 Position</button>
        </div>
        <?php endforeach; ?>
        <?php if (empty($salles)): ?>
        <p>Aucune salle disponible pour ces critères.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
