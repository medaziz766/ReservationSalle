<?php
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Model/Reservation.php';
require_once __DIR__ . '/../../Helper/Mailer.php';

$reservationController = new ReservationController();
$salleController = new SalleController();
$active = 'salles';

$salleId = $_GET['salleId'] ?? ($_POST['salleId'] ?? null);
if (!$salleId) { header("Location: salles.php"); exit; }

$salle = $salleController->getSalleById($salleId);
if (!$salle) { header("Location: salles.php"); exit; }

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = new Reservation(
        (int)$_POST['salleId'],
        $_SESSION['user_id'],
        trim($_POST['objet']),
        str_replace('T', ' ', $_POST['dateDebut']) . ':00',
        str_replace('T', ' ', $_POST['dateFin']) . ':00',
        'En attente',
        date('Y-m-d H:i:s')
    );
    $result = $reservationController->createReservation($r);
    if ($result === true) {
        Mailer::notifyReservationCreee($_SESSION['email'], $salle['nom'], $r->getDateDebut(), $r->getDateFin());
        $success = true;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réserver - <?= htmlspecialchars($salle['nom']) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Réserver « <?= htmlspecialchars($salle['nom']) ?> »</h2>

    <?php if ($success): ?>
        <div class="card" style="max-width:520px;margin:0 auto;">
            <p class="msg-success">Votre demande a bien été envoyée. Elle est en attente de validation par le gestionnaire. Un email de confirmation vous a été envoyé.</p>
            <a class="btn" href="mesReservations.php">Voir mes réservations</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?><p class="msg-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form id="reservationForm" class="user-form" method="POST" action="reserver.php?salleId=<?= $salleId ?>" novalidate>
            <input type="hidden" id="salleId" name="salleId" value="<?= $salleId ?>">

            <label for="objet">Objet de la réunion</label>
            <input type="text" id="objet" name="objet" placeholder="Enter meeting subject">

            <label for="dateDebut">Début</label>
            <input type="datetime-local" id="dateDebut" name="dateDebut">

            <label for="dateFin">Fin</label>
            <input type="datetime-local" id="dateFin" name="dateFin">

            <button type="submit" class="btn">Envoyer la demande</button>
        </form>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
<script src="assets/js/validateReservationUser.js"></script>
</body>
</html>
