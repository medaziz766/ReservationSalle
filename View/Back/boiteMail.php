<?php
require_once __DIR__ . '/../../config.php';
requireRole('Gestionnaire');
require_once __DIR__ . '/../../Controller/NotificationController.php';
$controller = new NotificationController();

if (isset($_GET['lu'])) {
    $controller->marquerCommeLue($_GET['lu']);
    header("Location: boiteMail.php");
    exit;
}
if (isset($_GET['tout_lu'])) {
    $controller->marquerToutesLuesGestionnaire();
    header("Location: boiteMail.php");
    exit;
}

$notifications = $controller->listPourGestionnaire();
$active = 'boiteMail';

function iconeType($type) {
    $map = [
        'NouvelleDemande' => '📥', 'DemandeModification' => '✏️',
        'PropositionAcceptee' => '✅', 'PropositionRefusee' => '❌', 'Annulee' => '🗑️'
    ];
    return $map[$type] ?? '💬';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<script src="assets/js/theme.js"></script>
<title>Boîte mail - Gestionnaire</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Boîte mail</h1>
    <p class="subtitle">Historique des demandes et réponses des utilisateurs (créations, modifications, annulations, réponses aux propositions).</p>

    <a href="boiteMail.php?tout_lu=1" class="btn-add" style="background:transparent; color:var(--text-dim); border:1px solid var(--line);">Tout marquer comme lu</a>

    <table class="admin-table">
        <tr><th></th><th>De</th><th>Message</th><th>Date</th><th></th></tr>
        <?php foreach ($notifications as $n): ?>
        <tr style="<?= $n['lu'] ? '' : 'font-weight:600;' ?>">
            <td><?= iconeType($n['type']) ?></td>
            <td><?= htmlspecialchars($n['user_prenom'] . ' ' . $n['user_nom']) ?></td>
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
        <tr><td colspan="5">Aucun message pour le moment.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
