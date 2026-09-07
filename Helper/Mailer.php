<?php
/**
 * Petit wrapper pour l'envoi de notifications par email.
 * Utilise la fonction mail() native de PHP (nécessite un serveur SMTP configuré
 * dans php.ini, ou un outil comme sendmail/Mailtrap en local).
 * Pour un envoi plus fiable en production, remplacer le corps de send()
 * par un appel à PHPMailer avec les identifiants SMTP de l'établissement.
 */
class Mailer
{
    public static function send($to, $subject, $message)
    {
        $headers = "From: no-reply@rooms.tn\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // En local (XAMPP/WAMP) mail() échoue souvent faute de serveur SMTP configuré.
        // On journalise donc toujours l'email dans un fichier de log pour pouvoir
        // vérifier son contenu même sans serveur mail actif.
        self::logEmail($to, $subject, $message);

        if (function_exists('mail')) {
            @mail($to, $subject, $message, $headers);
        }
    }

    private static function logEmail($to, $subject, $message)
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $entry = "[" . date('Y-m-d H:i:s') . "] To: $to | Subject: $subject\n$message\n----------------------\n";
        file_put_contents($logDir . '/emails.log', $entry, FILE_APPEND);
    }

    public static function notifyReservationCreee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Demande de réservation reçue - $nomSalle";
        $message = "<p>Bonjour,</p><p>Votre demande de réservation pour la salle <strong>$nomSalle</strong> 
                     du <strong>$dateDebut</strong> au <strong>$dateFin</strong> a bien été enregistrée 
                     et est en attente de validation.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyDemandeModification($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Demande de modification envoyée - $nomSalle";
        $message = "<p>Bonjour,</p><p>Votre demande de modification pour la salle <strong>$nomSalle</strong> 
                     (nouveau créneau : du <strong>$dateDebut</strong> au <strong>$dateFin</strong>) a bien été 
                     enregistrée et est de nouveau en attente de validation par le gestionnaire.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyReservationValidee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Réservation validée - $nomSalle";
        $message = "<p>Bonjour,</p><p>Votre réservation pour la salle <strong>$nomSalle</strong> 
                     du <strong>$dateDebut</strong> au <strong>$dateFin</strong> a été <strong>validée</strong>.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyReservationRefusee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Réservation refusée - $nomSalle";
        $message = "<p>Bonjour,</p><p>Votre réservation pour la salle <strong>$nomSalle</strong> 
                     du <strong>$dateDebut</strong> au <strong>$dateFin</strong> a été <strong>refusée</strong> 
                     en raison d'un conflit ou d'une indisponibilité.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyReservationAnnulee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Réservation annulée - $nomSalle";
        $message = "<p>Bonjour,</p><p>La réservation de la salle <strong>$nomSalle</strong> 
                     du <strong>$dateDebut</strong> au <strong>$dateFin</strong> a été annulée.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyReservationDeplacee($to, $ancienneSalle, $ancienDebut, $ancienneFin, $nouvelleSalle, $nouveauDebut, $nouvelleFin)
    {
        $subject = "Réunion déplacée - $nouvelleSalle";
        $message = "<p>Bonjour,</p><p>Votre réunion a été déplacée par le gestionnaire afin de résoudre un conflit de réservation.</p>"
                 . "<p><strong>Ancien créneau :</strong> $ancienneSalle, du $ancienDebut au $ancienneFin.<br>"
                 . "<strong>Nouveau créneau :</strong> $nouvelleSalle, du $nouveauDebut au $nouvelleFin.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyPropositionCreneau($to, $salleActuelle, $debutActuel, $finActuel, $salleProposee, $debutPropose, $finPropose)
    {
        $subject = "Nouveau créneau proposé pour votre réservation";
        $message = "<p>Bonjour,</p><p>Le gestionnaire vous propose un autre créneau pour votre réservation initiale "
                 . "(<strong>$salleActuelle</strong>, du $debutActuel au $finActuel), suite à un conflit.</p>"
                 . "<p><strong>Créneau proposé :</strong> $salleProposee, du <strong>$debutPropose</strong> au <strong>$finPropose</strong>.</p>"
                 . "<p>Connectez-vous à « Mes réservations » pour accepter ou refuser cette proposition.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyPropositionAcceptee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Nouveau créneau confirmé - $nomSalle";
        $message = "<p>Bonjour,</p><p>Vous avez accepté le créneau proposé. Votre réservation est confirmée : "
                 . "<strong>$nomSalle</strong>, du <strong>$dateDebut</strong> au <strong>$dateFin</strong>.</p>";
        self::send($to, $subject, $message);
    }

    public static function notifyPropositionRefusee($to, $nomSalle, $dateDebut, $dateFin)
    {
        $subject = "Proposition refusée - $nomSalle";
        $message = "<p>Bonjour,</p><p>Vous avez refusé le créneau proposé par le gestionnaire. Votre réservation initiale "
                 . "(<strong>$nomSalle</strong>, du $dateDebut au $dateFin) a donc été <strong>refusée</strong>.</p>";
        self::send($to, $subject, $message);
    }
}
