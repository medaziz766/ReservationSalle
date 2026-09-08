<?php
require_once __DIR__ . '/../../Controller/NotificationController.php';
$controller = new NotificationController();

if (isset($_GET['lu'])) {
    $controller->marquerCommeLue($_GET['lu']);
    header("Location: boiteMail.php");
    exit;
}
if (isset($_GET['tout_lu'])) {
    $controller->marquerToutesLuesUtilisateur($_SESSION['user_id']);
    header("Location: boiteMail.php");
    exit;
}

$notifications = $controller->listPourUtilisateur($_SESSION['user_id']);
$active = 'boiteMail';

function iconeTypeUser($type) {
    $map = [
        'Validee' => '✅', 'Refusee' => '❌', 'Proposition' => '📩'
    ];
    return $map[$type] ?? '💬';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Boîte mail - RoomBooking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Boîte mail</h2>
    <p style="color:var(--text-dim);">Historique des échanges avec le gestionnaire concernant vos réservations.</p>

    <a href="boiteMail.php?tout_lu=1" class="btn" style="background:transparent; color:var(--text-dim); border:1px solid var(--line);">Tout marquer comme lu</a>

    <table class="simple-table" style="margin-top:16px;">
        <tr><th></th><th>Message</th><th>Date</th><th></th></tr>
        <?php foreach ($notifications as $n): ?>
        <tr style="<?= $n['lu'] ? '' : 'font-weight:600;' ?>">
            <td><?= iconeTypeUser($n['type']) ?></td>
            <td><?= htmlspecialchars($n['message']) ?></td>
            <td class="mono" style="white-space:nowrap;"><?= htmlspecialchars($n['date_creation']) ?></td>
            <td>
                <?php if (!$n['lu']): ?>
                <a href="boiteMail.php?lu=<?= $n['id'] ?>" style="font-size:12px;">marquer lu</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?>
        <tr><td colspan="4">Aucun message pour le moment.</td></tr>
        <?php endif; ?>
    </table>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
