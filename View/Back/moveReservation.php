<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Model/Reservation.php';

$reservationController = new ReservationController();
$salleController = new SalleController();
$active = 'conflits';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: conflits.php"); exit; }

$data = $reservationController->getReservationById($id);
if (!$data) { header("Location: conflits.php"); exit; }

$salles = array_filter($salleController->listSalles(), function ($salle) use ($data) {
    return $salle['statut'] === 'Disponible' || (int)$salle['id'] === (int)$data['salle_id'];
});
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = new Reservation(
        (int)($_POST['salleId'] ?? 0),
        null,
        trim($_POST['objet'] ?? ''),
        str_replace('T', ' ', $_POST['dateDebut'] ?? '') . ':00',
        str_replace('T', ' ', $_POST['dateFin'] ?? '') . ':00',
        null,
        null,
        $data['id']
    );
    $result = $reservationController->updateReservation($r);
    if ($result === true) {
        header("Location: conflits.php?deplacement=ok");
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
<title>Déplacer une réservation - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Déplacer la réunion</h1>
    <p class="subtitle">Modifiez uniquement la salle, uniquement le créneau, ou les deux. Le système vérifie la disponibilité avant l'enregistrement et notifie le demandeur.</p>

    <?php if ($error): ?><p class="msg-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form id="reservationForm" class="admin-form" method="POST" action="moveReservation.php?id=<?= $data['id'] ?>" novalidate>
        <label for="salleId">Salle</label>
        <select id="salleId" name="salleId" required>
            <?php foreach ($salles as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $data['salle_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom'] . ' (' . $s['batiment_nom'] . ')') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="objet">Objet</label>
        <input type="text" id="objet" name="objet" value="<?= htmlspecialchars($data['objet']) ?>" minlength="3" required>

        <label for="dateDebut">Début</label>
        <input type="datetime-local" id="dateDebut" name="dateDebut" value="<?= str_replace(' ', 'T', substr($data['date_debut'], 0, 16)) ?>" required>

        <label for="dateFin">Fin</label>
        <input type="datetime-local" id="dateFin" name="dateFin" value="<?= str_replace(' ', 'T', substr($data['date_fin'], 0, 16)) ?>" required>

        <button type="submit" class="btn">Enregistrer le déplacement</button>
    </form>
</div>
<script src="assets/js/validateReservation.js"></script>
</body>
</html>
