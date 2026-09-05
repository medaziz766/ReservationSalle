<?php
class Utilisateur
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $password;
    private $role;
    private $dateCreation;

    public function __construct($nom, $prenom, $email, $password, $role, $dateCreation, $id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->dateCreation = $dateCreation;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getDateCreation() { return $this->dateCreation; }

    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setRole($role) { $this->role = $role; }

    public function show()
    {
        echo "<table border='1' cellpadding='8'>
                <tr><th>Nom</th><td>{$this->nom} {$this->prenom}</td></tr>
                <tr><th>Email</th><td>{$this->email}</td></tr>
                <tr><th>Rôle</th><td>{$this->role}</td></tr>
              </table>";
    }
}
