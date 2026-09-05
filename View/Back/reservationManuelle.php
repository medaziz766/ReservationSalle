<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/Reservation.php';

$reservationController = new ReservationController();
$salleController = new SalleController();
$utilisateurController = new UtilisateurController();

$salles = $salleController->listSalles();
$utilisateurs = $utilisateurController->listUtilisateurs();
$active = 'manuelle';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = new Reservation(
        (int)$_POST['salleId'],
        (int)$_POST['utilisateurId'],
        trim($_POST['objet']),
        str_replace('T', ' ', $_POST['dateDebut']) . ':00',
        str_replace('T', ' ', $_POST['dateFin']) . ':00',
        'Validée', // créée directement validée car saisie manuellement par le gestionnaire
        date('Y-m-d H:i:s')
    );
    $result = $reservationController->createReservation($r);
    if ($result === true) {
        header("Location: demandes.php");
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
<title>Réservation manuelle - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Créer une réservation manuelle</h1>
    <p class="subtitle">Réserver une salle directement pour un utilisateur (statut : validée).</p>

    <?php if ($error): ?><p class="msg-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form id="reservationForm" class="admin-form" method="POST" action="reservationManuelle.php" novalidate>
        <label for="salleId">Salle</label>
        <select id="salleId" name="salleId">
            <option value="">-- choisir --</option>
            <?php foreach ($salles as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom'] . ' (' . $s['batiment_nom'] . ')') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="utilisateurId">Utilisateur</label>
        <select id="utilisateurId" name="utilisateurId">
            <option value="">-- choisir --</option>
            <?php foreach ($utilisateurs as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom'] . ' (' . $u['email'] . ')') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="objet">Objet</label>
        <input type="text" id="objet" name="objet" placeholder="Enter meeting subject">

        <label for="dateDebut">Début</label>
        <input type="datetime-local" id="dateDebut" name="dateDebut">

        <label for="dateFin">Fin</label>
        <input type="datetime-local" id="dateFin" name="dateFin">

        <button type="submit" class="btn">Créer la réservation</button>
    </form>
</div>
<script src="assets/js/validateReservation.js"></script>
</body>
</html>
