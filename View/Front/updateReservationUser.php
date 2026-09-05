<?php
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Model/Reservation.php';
$controller = new ReservationController();
$active = 'mesReservations';

define('DELAI_LIMITE_HEURES', 24);

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: mesReservations.php"); exit; }

$data = $controller->getReservationById($id);
if (!$data || $data['utilisateur_id'] != $_SESSION['user_id']) {
    header("Location: mesReservations.php"); exit;
}
if ((strtotime($data['date_debut']) - time()) <= DELAI_LIMITE_HEURES * 3600) {
    header("Location: mesReservations.php"); exit; // délai dépassé, retour sans modification
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = new Reservation(
        $data['salle_id'], // la salle ne change pas ici (l'utilisateur modifie son créneau/objet)
        null,
        trim($_POST['objet']),
        str_replace('T', ' ', $_POST['dateDebut']) . ':00',
        str_replace('T', ' ', $_POST['dateFin']) . ':00',
        null,
        null,
        $id
    );
    $result = $controller->updateReservation($r);
    if ($result === true) {
        header("Location: mesReservations.php");
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
<title>Modifier ma réservation - RoomBooking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Modifier ma réservation — <?= htmlspecialchars($data['salle_nom']) ?></h2>

    <?php if ($error): ?><p class="msg-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form id="reservationForm" class="user-form" method="POST" action="updateReservationUser.php?id=<?= $id ?>" novalidate>
        <label for="objet">Objet</label>
        <input type="text" id="objet" name="objet" value="<?= htmlspecialchars($data['objet']) ?>">

        <label for="dateDebut">Début</label>
        <input type="datetime-local" id="dateDebut" name="dateDebut" value="<?= str_replace(' ', 'T', substr($data['date_debut'], 0, 16)) ?>">

        <label for="dateFin">Fin</label>
        <input type="datetime-local" id="dateFin" name="dateFin" value="<?= str_replace(' ', 'T', substr($data['date_fin'], 0, 16)) ?>">

        <button type="submit" class="btn">Enregistrer les modifications</button>
    </form>
</section>

<?php include 'footer.php'; ?>
<script src="assets/js/validateReservationUser.js"></script>
</body>
</html>
