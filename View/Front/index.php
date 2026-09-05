<?php
require_once __DIR__ . '/../../Controller/ReservationController.php';
$controller = new ReservationController();
$mesReservations = $controller->listByUtilisateur($_SESSION['user_id']);
$prochaines = array_filter($mesReservations, fn($r) => $r['statut'] !== 'Annulée' && strtotime($r['date_debut']) >= time());
$active = 'home';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>RoomBooking - Accueil</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="hero">
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> 👋</h1>
    <p>Consultez les salles disponibles, réservez un créneau et suivez vos réunions en un clin d'œil.</p>
    <a class="btn" href="salles.php">Trouver une salle</a>
</section>

<section>
    <h2>Vos prochaines réservations</h2>
    <div class="grid">
        <?php foreach (array_slice($prochaines, 0, 4) as $r): ?>
        <div class="card">
            <h3><?= htmlspecialchars($r['objet']) ?></h3>
            <p><?= htmlspecialchars($r['salle_nom']) ?> — <?= htmlspecialchars($r['batiment_nom']) ?></p>
            <p style="color:var(--text-dim); font-size:13px;"><?= htmlspecialchars($r['date_debut']) ?> → <?= htmlspecialchars($r['date_fin']) ?></p>
            <span class="badge badge-<?= $r['statut']==='Validée'?'success':($r['statut']==='Refusée'?'danger':'warning') ?>"><?= htmlspecialchars($r['statut']) ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($prochaines)): ?>
        <p>Aucune réservation à venir. <a href="salles.php">Réservez une salle</a>.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
