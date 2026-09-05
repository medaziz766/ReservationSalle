<?php
require_once __DIR__ . '/../../Controller/SalleController.php';
require_once __DIR__ . '/../../Controller/ReservationController.php';
$salleController = new SalleController();
$reservationController = new ReservationController();
$active = 'salles';

$salleId = $_GET['salleId'] ?? null;
if (!$salleId) { header("Location: salles.php"); exit; }

$salle = $salleController->getSalleById($salleId);
if (!$salle) { header("Location: salles.php"); exit; }

$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('n'));

$reservations = $reservationController->listBySalleForCalendar($salleId, $year, $month);

// Regrouper les réservations par jour (numéro du jour => liste d'objets)
$parJour = [];
foreach ($reservations as $r) {
    $day = (int)date('j', strtotime($r['date_debut']));
    $parJour[$day][] = $r;
}

$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int)date('t', $firstDayOfMonth);
$startWeekday = (int)date('N', $firstDayOfMonth); // 1 (lundi) - 7 (dimanche)

$prevMonth = $month - 1; $prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
$nextMonth = $month + 1; $nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

$moisNoms = ['', 'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Calendrier - <?= htmlspecialchars($salle['nom']) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section>
    <h2>Calendrier — <?= htmlspecialchars($salle['nom']) ?> (<?= htmlspecialchars($salle['batiment_nom']) ?>)</h2>
    <p style="color:var(--text-dim);">Capacité <?= (int)$salle['capacite'] ?> personnes — <?= htmlspecialchars($salle['equipements']) ?></p>
    <a class="btn" href="reserver.php?salleId=<?= $salleId ?>">+ Réserver cette salle</a>

    <div class="calendar" style="margin-top:20px;">
        <div class="calendar-header">
            <a href="calendrier.php?salleId=<?= $salleId ?>&year=<?= $prevYear ?>&month=<?= $prevMonth ?>">← Mois précédent</a>
            <strong><?= $moisNoms[$month] ?> <?= $year ?></strong>
            <a href="calendrier.php?salleId=<?= $salleId ?>&year=<?= $nextYear ?>&month=<?= $nextMonth ?>">Mois suivant →</a>
        </div>
        <div class="calendar-grid">
            <?php foreach (['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $dn): ?>
            <div class="day-name"><?= $dn ?></div>
            <?php endforeach; ?>

            <?php for ($i = 1; $i < $startWeekday; $i++): ?>
            <div class="day-cell empty"></div>
            <?php endfor; ?>

            <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
            <div class="day-cell">
                <div class="day-num"><?= $day ?></div>
                <?php if (isset($parJour[$day])): foreach ($parJour[$day] as $ev): ?>
                <span class="event-dot" title="<?= htmlspecialchars($ev['date_debut'] . ' - ' . $ev['date_fin']) ?>">
                    <?= date('H:i', strtotime($ev['date_debut'])) ?> <?= htmlspecialchars(mb_substr($ev['objet'], 0, 12)) ?>
                </span>
                <?php endforeach; endif; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
