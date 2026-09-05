<?php
class Reservation
{
    private $id;
    private $salleId;
    private $utilisateurId;
    private $objet;
    private $dateDebut;
    private $dateFin;
    private $statut;
    private $dateCreation;

    public function __construct($salleId, $utilisateurId, $objet, $dateDebut, $dateFin, $statut, $dateCreation, $id = null)
    {
        $this->id = $id;
        $this->salleId = $salleId;
        $this->utilisateurId = $utilisateurId;
        $this->objet = $objet;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->statut = $statut;
        $this->dateCreation = $dateCreation;
    }

    public function getId() { return $this->id; }
    public function getSalleId() { return $this->salleId; }
    public function getUtilisateurId() { return $this->utilisateurId; }
    public function getObjet() { return $this->objet; }
    public function getDateDebut() { return $this->dateDebut; }
    public function getDateFin() { return $this->dateFin; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->dateCreation; }

    public function setId($id) { $this->id = $id; }
    public function setSalleId($salleId) { $this->salleId = $salleId; }
    public function setUtilisateurId($utilisateurId) { $this->utilisateurId = $utilisateurId; }
    public function setObjet($objet) { $this->objet = $objet; }
    public function setDateDebut($dateDebut) { $this->dateDebut = $dateDebut; }
    public function setDateFin($dateFin) { $this->dateFin = $dateFin; }
    public function setStatut($statut) { $this->statut = $statut; }

    public function show()
    {
        echo "<table border='1' cellpadding='8'>
                <tr><th>Objet</th><td>{$this->objet}</td></tr>
                <tr><th>Début</th><td>{$this->dateDebut}</td></tr>
                <tr><th>Fin</th><td>{$this->dateFin}</td></tr>
                <tr><th>Statut</th><td>{$this->statut}</td></tr>
              </table>";
    }
}
