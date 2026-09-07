<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/SalleController.php';

$reservationController = new ReservationController();
$salleController = new SalleController();
$active = 'conflits';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: conflits.php"); exit; }

$data = $reservationController->getReservationById($id);
if (!$data) { header("Location: conflits.php"); exit; }

$salles = array_filter($salleController->listSalles(), function ($salle) {
    return $salle['statut'] === 'Disponible';
});
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $salleId = (int)($_POST['salleId'] ?? 0);
    $dateDebut = str_replace('T', ' ', $_POST['dateDebut'] ?? '') . ':00';
    $dateFin = str_replace('T', ' ', $_POST['dateFin'] ?? '') . ':00';

    $result = $reservationController->proposerCreneau($id, $salleId, $dateDebut, $dateFin);
    if ($result === true) {
        header("Location: conflits.php?proposition=ok");
        exit;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Proposer un créneau - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Proposer un autre créneau</h1>
    <p class="subtitle">
        Réservation initiale : <strong><?= htmlspecialchars($data['salle_nom']) ?></strong>,
        du <?= htmlspecialchars($data['date_debut']) ?> au <?= htmlspecialchars($data['date_fin']) ?>
        (<?= htmlspecialchars($data['objet']) ?>).<br>
        L'utilisateur devra accepter ou refuser cette proposition depuis « Mes réservations ».
        La réservation initiale n'est pas modifiée tant qu'il n'a pas répondu.
    </p>

    <?php if ($error): ?><p class="msg-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form id="reservationForm" class="admin-form" method="POST" action="proposerCreneau.php?id=<?= $data['id'] ?>" novalidate>
        <label for="salleId">Salle proposée</label>
        <select id="salleId" name="salleId">
            <?php foreach ($salles as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom'] . ' (' . $s['batiment_nom'] . ')') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="dateDebut">Début proposé</label>
        <input type="datetime-local" id="dateDebut" name="dateDebut">

        <label for="dateFin">Fin proposée</label>
        <input type="datetime-local" id="dateFin" name="dateFin">

        <button type="submit" class="btn">Envoyer la proposition</button>
    </form>
</div>
<script src="assets/js/validateReservation.js"></script>
</body>
</html>
