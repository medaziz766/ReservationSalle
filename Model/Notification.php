<?php
class Notification
{
    private $id;
    private $utilisateurId;
    private $reservationId;
    private $destinataireRole;
    private $type;
    private $message;
    private $lu;
    private $dateCreation;

    public function __construct($utilisateurId, $reservationId, $destinataireRole, $type, $message, $dateCreation, $lu = false, $id = null)
    {
        $this->id = $id;
        $this->utilisateurId = $utilisateurId;
        $this->reservationId = $reservationId;
        $this->destinataireRole = $destinataireRole;
        $this->type = $type;
        $this->message = $message;
        $this->dateCreation = $dateCreation;
        $this->lu = $lu;
    }

    public function getId() { return $this->id; }
    public function getUtilisateurId() { return $this->utilisateurId; }
    public function getReservationId() { return $this->reservationId; }
    public function getDestinataireRole() { return $this->destinataireRole; }
    public function getType() { return $this->type; }
    public function getMessage() { return $this->message; }
    public function getLu() { return $this->lu; }
    public function getDateCreation() { return $this->dateCreation; }

    public function show()
    {
        echo "<table border='1' cellpadding='8'>
                <tr><th>Type</th><td>{$this->type}</td></tr>
                <tr><th>Message</th><td>{$this->message}</td></tr>
                <tr><th>Date</th><td>{$this->dateCreation}</td></tr>
              </table>";
    }
}
