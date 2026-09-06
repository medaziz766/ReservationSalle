<?php
require_once __DIR__ . '/../../config.php';
requireRole('Admin');
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

$controller = new ReservationController();

$dateDebut = $_GET['dateDebut'] ?? date('Y-m-01');
$dateFin = $_GET['dateFin'] ?? date('Y-m-t');
$reservations = $controller->rapportParPeriode($dateDebut . ' 00:00:00', $dateFin . ' 23:59:59');

// Encodage : FPDF de base ne gère pas l'UTF-8, on convertit chaque champ affiché
function fp($text) {
    return iconv('UTF-8', 'windows-1252//TRANSLIT', (string)$text);
}

class RapportPDF extends FPDF
{
    public $periode = '';

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 8, iconv('UTF-8', 'windows-1252//TRANSLIT', 'RoomBooking - Rapport de reservations'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', 'Periode : ' . $this->periode), 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(4);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(130, 130, 130);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}  -  Genere le ' . date('d/m/Y H:i'), 0, 0, 'C');
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(37, 84, 255);
        $this->SetTextColor(255, 255, 255);
        $widths = [38, 30, 30, 30, 30, 24];
        $headers = ['Salle', 'Batiment', 'Debut', 'Fin', 'Duree', 'Statut'];
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], 8, $h, 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
    }
}

$pdf = new RapportPDF();
$pdf->periode = date('d/m/Y', strtotime($dateDebut)) . ' au ' . date('d/m/Y', strtotime($dateFin));
$pdf->AliasNbPages();
$pdf->AddPage('L'); // paysage pour plus de largeur
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 8);
$widths = [38, 30, 30, 30, 30, 24];
$fill = false;

$countValidees = 0; $countAttente = 0; $countRefusees = 0; $countAnnulees = 0;

foreach ($reservations as $r) {
    switch ($r['statut']) {
        case 'Validée': $countValidees++; break;
        case 'En attente': $countAttente++; break;
        case 'Refusée': $countRefusees++; break;
        case 'Annulée': $countAnnulees++; break;
    }

    $debut = strtotime($r['date_debut']);
    $fin = strtotime($r['date_fin']);
    $dureeMin = round(($fin - $debut) / 60);
    $duree = floor($dureeMin / 60) . 'h' . str_pad($dureeMin % 60, 2, '0', STR_PAD_LEFT);

    $pdf->SetFillColor(245, 247, 250);
    $pdf->Cell($widths[0], 7, fp($r['salle_nom']), 1, 0, 'L', $fill);
    $pdf->Cell($widths[1], 7, fp($r['batiment_nom']), 1, 0, 'L', $fill);
    $pdf->Cell($widths[2], 7, date('d/m/Y H:i', $debut), 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 7, date('d/m/Y H:i', $fin), 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 7, $duree, 1, 0, 'C', $fill);
    $pdf->Cell($widths[5], 7, fp($r['statut']), 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill;

    if ($pdf->GetY() > 175) {
        $pdf->AddPage('L');
        $pdf->TableHeader();
        $pdf->SetFont('Arial', '', 8);
    }
}

if (empty($reservations)) {
    $pdf->Cell(array_sum($widths), 10, 'Aucune reservation sur cette periode.', 1, 1, 'C');
}

// Résumé en bas de rapport
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Resume', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, 'Total reservations : ' . count($reservations), 0, 1);
$pdf->Cell(0, 6, 'Validees : ' . $countValidees . '   |   En attente : ' . $countAttente . '   |   Refusees : ' . $countRefusees . '   |   Annulees : ' . $countAnnulees, 0, 1);

$pdf->Output('D', 'rapport_reservations_' . $dateDebut . '_au_' . $dateFin . '.pdf');
