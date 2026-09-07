<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
$controller = new ReservationController();

if (isset($_GET['action'], $_GET['id'])) {
    if ($_GET['action'] === 'valider') $controller->validerReservation($_GET['id']);
    if ($_GET['action'] === 'refuser') $controller->refuserReservation($_GET['id']);
    header("Location: demandes.php");
    exit;
}

$reservations = array_filter($controller->listReservations(), fn($r) => $r['statut'] === 'En attente');
$active = 'demandes';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Demandes - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Demandes de réservation</h1>
    <p class="subtitle">Valider, refuser, ou proposer un autre créneau pour les demandes en attente.</p>

    <table class="admin-table">
        <tr><th>Type</th><th>Salle</th><th>Bâtiment</th><th>Demandeur</th><th>Objet</th><th>Début</th><th>Fin</th><th>Actions</th></tr>
        <?php foreach ($reservations as $r): ?>
        <tr>
            <td><span class="badge <?= $r['type_demande'] === 'Modification' ? 'badge-warning' : 'badge-info' ?>"><?= htmlspecialchars($r['type_demande']) ?></span></td>
            <td><?= htmlspecialchars($r['salle_nom']) ?></td>
            <td><?= htmlspecialchars($r['batiment_nom']) ?></td>
            <td><?= htmlspecialchars($r['user_prenom'] . ' ' . $r['user_nom']) ?></td>
            <td><?= htmlspecialchars($r['objet']) ?></td>
            <td><?= htmlspecialchars($r['date_debut']) ?></td>
            <td><?= htmlspecialchars($r['date_fin']) ?></td>
            <td class="actions">
                <?php if (!empty($r['proposition_salle_id'])): ?>
                    <span class="badge badge-warning">Proposition envoyée — en attente de réponse</span>
                <?php else: ?>
                    <a href="demandes.php?action=valider&id=<?= $r['id'] ?>" class="validate">valider</a>
                    <a href="demandes.php?action=refuser&id=<?= $r['id'] ?>" class="refuse">refuser</a>
                    <a href="proposerCreneau.php?id=<?= $r['id'] ?>" class="update">proposer un créneau</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($reservations)): ?>
        <tr><td colspan="8">Aucune demande en attente.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
