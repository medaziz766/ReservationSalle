<?php
class Salle
{
    private $id;
    private $batimentId;
    private $etage;
    private $nom;
    private $capacite;
    private $equipements;
    private $statut;

    public function __construct($batimentId, $etage, $nom, $capacite, $equipements, $statut, $id = null)
    {
        $this->id = $id;
        $this->batimentId = $batimentId;
        $this->etage = $etage;
        $this->nom = $nom;
        $this->capacite = $capacite;
        $this->equipements = $equipements;
        $this->statut = $statut;
    }

    public function getId() { return $this->id; }
    public function getBatimentId() { return $this->batimentId; }
    public function getEtage() { return $this->etage; }
    public function getNom() { return $this->nom; }
    public function getCapacite() { return $this->capacite; }
    public function getEquipements() { return $this->equipements; }
    public function getStatut() { return $this->statut; }

    public function setId($id) { $this->id = $id; }
    public function setBatimentId($batimentId) { $this->batimentId = $batimentId; }
    public function setEtage($etage) { $this->etage = $etage; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setCapacite($capacite) { $this->capacite = $capacite; }
    public function setEquipements($equipements) { $this->equipements = $equipements; }
    public function setStatut($statut) { $this->statut = $statut; }

    public function show()
    {
        echo "<table border='1' cellpadding='8'>
                <tr><th>Nom</th><td>{$this->nom}</td></tr>
                <tr><th>Étage</th><td>{$this->etage}</td></tr>
                <tr><th>Capacité</th><td>{$this->capacite}</td></tr>
                <tr><th>Équipements</th><td>{$this->equipements}</td></tr>
                <tr><th>Statut</th><td>{$this->statut}</td></tr>
              </table>";
    }
}
