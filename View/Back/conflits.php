<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
$controller = new ReservationController();

if (isset($_GET['refuser'])) {
    $controller->refuserReservation($_GET['refuser']);
    header("Location: conflits.php");
    exit;
}

$conflits = $controller->listConflits();
$active = 'conflits';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Conflits - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Conflits de réservation</h1>
    <p class="subtitle">Créneaux qui se chevauchent sur une même salle. Refusez une réservation ou proposez-lui un autre créneau.</p>

    <?php if (($_GET['proposition'] ?? '') === 'ok'): ?>
        <p class="msg-success">La proposition a été envoyée à l'utilisateur, qui doit l'accepter ou la refuser.</p>
    <?php endif; ?>

    <?php if (empty($conflits)): ?>
        <p class="msg-success">Aucun conflit détecté actuellement.</p>
    <?php endif; ?>

    <?php foreach ($conflits as $c): ?>
    <table class="admin-table" style="margin-bottom:18px;">
        <tr>
            <th colspan="4">⚠️ Conflit sur la salle « <?= htmlspecialchars($c['salle_nom']) ?> » (<?= htmlspecialchars($c['batiment_nom']) ?>)</th>
        </tr>
        <tr><th>Objet</th><th>Créneau</th><th>Demandeur</th><th>Action</th></tr>
        <tr>
            <td><?= htmlspecialchars($c['objet1']) ?></td>
            <td><?= htmlspecialchars($c['debut1']) ?> → <?= htmlspecialchars($c['fin1']) ?></td>
            <td><?= htmlspecialchars($c['email1']) ?></td>
            <td class="actions">
                <a href="conflits.php?refuser=<?= $c['id1'] ?>" class="refuse">refuser cette réservation</a>
                <a href="proposerCreneau.php?id=<?= $c['id1'] ?>" class="validate">proposer un créneau</a>
            </td>
        </tr>
        <tr>
            <td><?= htmlspecialchars($c['objet2']) ?></td>
            <td><?= htmlspecialchars($c['debut2']) ?> → <?= htmlspecialchars($c['fin2']) ?></td>
            <td><?= htmlspecialchars($c['email2']) ?></td>
            <td class="actions">
                <a href="conflits.php?refuser=<?= $c['id2'] ?>" class="refuse">refuser cette réservation</a>
                <a href="proposerCreneau.php?id=<?= $c['id2'] ?>" class="validate">proposer un créneau</a>
            </td>
        </tr>
    </table>
    <?php endforeach; ?>
</div>
</body>
</html>
