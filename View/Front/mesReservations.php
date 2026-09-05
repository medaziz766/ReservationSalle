<?php
require_once __DIR__ . '/../../Controller/ReservationController.php';
$controller = new ReservationController();
$active = 'mesReservations';

// Délai limite pour modifier/annuler : 24h avant le début de la réunion
define('DELAI_LIMITE_HEURES', 24);

if (isset($_GET['annuler'])) {
    $r = $controller->getReservationById($_GET['annuler']);
    if ($r && $r['utilisateur_id'] == $_SESSION['user_id'] && (strtotime($r['date_debut']) - time()) > DELAI_LIMITE_HEURES * 3600) {
        $controller->annulerReservation($_GET['annuler']);
    }
    header("Location: mesReservations.php");
    exit;
}

$mesReservations = $controller->listByUtilisateur($_SESSION['user_id']);

function statutBadgeU($statut) {
    $map = ['Validée' => 'badge-success', 'En attente' => 'badge-warning', 'Refusée' => 'badge-danger', 'Annulée' => 'badge-info'];
    $cls = $map[$statut] ?? 'badge-info';
    return "<span class='badge $cls'>" . htmlspecialchars($statut) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes réservations - RoomBooking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Mes réservations</h2>
    <p style="color:var(--text-dim);">Vous pouvez modifier ou annuler une réservation jusqu'à <?= DELAI_LIMITE_HEURES ?>h avant le début de la réunion.</p>

    <table class="simple-table">
        <tr><th>Salle</th><th>Objet</th><th>Début</th><th>Fin</th><th>Statut</th><th>Actions</th></tr>
        <?php foreach ($mesReservations as $r):
            $modifiable = in_array($r['statut'], ['En attente', 'Validée']) && (strtotime($r['date_debut']) - time()) > DELAI_LIMITE_HEURES * 3600;
        ?>
        <tr>
            <td><?= htmlspecialchars($r['salle_nom']) ?> (<?= htmlspecialchars($r['batiment_nom']) ?>)</td>
            <td><?= htmlspecialchars($r['objet']) ?></td>
            <td><?= htmlspecialchars($r['date_debut']) ?></td>
            <td><?= htmlspecialchars($r['date_fin']) ?></td>
            <td><?= statutBadgeU($r['statut']) ?></td>
            <td>
                <?php if ($modifiable): ?>
                    <a href="updateReservationUser.php?id=<?= $r['id'] ?>">modifier</a> ·
                    <a href="mesReservations.php?annuler=<?= $r['id'] ?>" onclick="return confirm('Annuler cette réservation ?');" style="color:var(--danger);">annuler</a>
                <?php else: ?>
                    <span style="color:var(--text-dim); font-size:12px;">non modifiable</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($mesReservations)): ?>
        <tr><td colspan="6">Vous n'avez pas encore de réservation. <a href="salles.php">Réservez une salle</a>.</td></tr>
        <?php endif; ?>
    </table>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
