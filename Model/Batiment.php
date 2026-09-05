<?php
class Batiment
{
    private $id;
    private $nom;
    private $adresse;
    private $nombreEtages;

    public function __construct($nom, $adresse, $nombreEtages, $id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->nombreEtages = $nombreEtages;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getAdresse() { return $this->adresse; }
    public function getNombreEtages() { return $this->nombreEtages; }

    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setAdresse($adresse) { $this->adresse = $adresse; }
    public function setNombreEtages($nombreEtages) { $this->nombreEtages = $nombreEtages; }

    public function show()
    {
        echo "<table border='1' cellpadding='8'>
                <tr><th>Nom</th><td>{$this->nom}</td></tr>
                <tr><th>Adresse</th><td>{$this->adresse}</td></tr>
                <tr><th>Étages</th><td>{$this->nombreEtages}</td></tr>
              </table>";
    }
}
