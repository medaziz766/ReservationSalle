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

// Liste complète (non filtrée) pour construire le menu déroulant des salles
$toutesMesReservations = $controller->listByUtilisateur($_SESSION['user_id']);
$sallesDistinctes = [];
foreach ($toutesMesReservations as $r) {
    $sallesDistinctes[$r['salle_id']] = $r['salle_nom'];
}

$statut = $_GET['statut'] ?? '';
$salleId = $_GET['salleId'] ?? '';

$mesReservations = $controller->listByUtilisateurFiltre($_SESSION['user_id'], $statut ?: null, $salleId ?: null);

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
    <?php if (($_GET['demande'] ?? '') === 'envoyee'): ?>
    <p class="msg-success">Votre demande de modification a été envoyée et est de nouveau en attente de validation.</p>
    <?php endif; ?>
    <p style="color:var(--text-dim);">Vous pouvez modifier ou annuler une réservation jusqu'à <?= DELAI_LIMITE_HEURES ?>h avant le début de la réunion. Toute modification repasse la réservation en attente de validation.</p>

    <form class="filter-bar" method="GET" action="mesReservations.php">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <?php foreach (['En attente','Validée','Refusée','Annulée'] as $st): ?>
            <option value="<?= $st ?>" <?= $statut === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
        <select name="salleId">
            <option value="">Toutes les salles</option>
            <?php foreach ($sallesDistinctes as $id => $nom): ?>
            <option value="<?= $id ?>" <?= $salleId == $id ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
        <?php if ($statut || $salleId): ?><a href="mesReservations.php" class="btn" style="background:transparent; color:var(--text-dim); border:1px solid var(--line); margin-top:0;">Réinitialiser</a><?php endif; ?>
    </form>

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
        <tr><td colspan="6">Aucune réservation ne correspond à ces critères. <a href="salles.php">Réservez une salle</a>.</td></tr>
        <?php endif; ?>
    </table>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
