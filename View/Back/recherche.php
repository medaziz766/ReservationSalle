<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/SalleController.php';
$reservationController = new ReservationController();
$salleController = new SalleController();
$salles = $salleController->listSalles();
$active = 'recherche';

$salleId = $_GET['salleId'] ?? '';
$statut = $_GET['statut'] ?? '';
$dateDebut = $_GET['dateDebut'] ?? '';
$dateFin = $_GET['dateFin'] ?? '';
$email = $_GET['email'] ?? '';

$hasSearched = isset($_GET['search']);
$results = $hasSearched
    ? $reservationController->rechercheMulticritere(
        $salleId ?: null,
        $statut ?: null,
        $dateDebut ? $dateDebut . ' 00:00:00' : null,
        $dateFin ? $dateFin . ' 23:59:59' : null,
        $email ?: null
      )
    : [];

function statutBadgeS($statut) {
    $map = ['Validée' => 'badge-success', 'En attente' => 'badge-warning', 'Refusée' => 'badge-danger', 'Annulée' => 'badge-info'];
    $cls = $map[$statut] ?? 'badge-info';
    return "<span class='badge $cls'>" . htmlspecialchars($statut) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Recherche - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Recherche multicritère</h1>
    <p class="subtitle">Rechercher des réservations par salle, statut, période ou utilisateur.</p>

    <form class="filter-bar" method="GET" action="recherche.php">
        <input type="hidden" name="search" value="1">
        <select name="salleId">
            <option value="">Toutes les salles</option>
            <?php foreach ($salles as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $salleId == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="statut">
            <option value="">Tous les statuts</option>
            <?php foreach (['En attente','Validée','Refusée','Annulée'] as $st): ?>
            <option value="<?= $st ?>" <?= $statut === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="dateDebut" value="<?= htmlspecialchars($dateDebut) ?>" placeholder="Du">
        <input type="date" name="dateFin" value="<?= htmlspecialchars($dateFin) ?>" placeholder="Au">
        <input type="text" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email utilisateur">
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($hasSearched): ?>
    <table class="admin-table">
        <tr><th>Salle</th><th>Bâtiment</th><th>Demandeur</th><th>Objet</th><th>Début</th><th>Fin</th><th>Statut</th></tr>
        <?php foreach ($results as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['salle_nom']) ?></td>
            <td><?= htmlspecialchars($r['batiment_nom']) ?></td>
            <td><?= htmlspecialchars($r['user_prenom'] . ' ' . $r['user_nom']) ?></td>
            <td><?= htmlspecialchars($r['objet']) ?></td>
            <td><?= htmlspecialchars($r['date_debut']) ?></td>
            <td><?= htmlspecialchars($r['date_fin']) ?></td>
            <td><?= statutBadgeS($r['statut']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
        <tr><td colspan="7">Aucun résultat pour ces critères.</td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
