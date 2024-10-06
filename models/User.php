<?php
class User {
    private $conn;
    private $id;
    private $username;
    private $email;  // New email property
    private $password;
    private $role;
    private $date_of_birth;
    private $cin;
    private $photo;
    private $numtelephone;

    private $nom;
    private $prenom;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Setters
    public function setId(int $id) {
        $this->id = $id;
    }

    public function setUsername(string $val) {
        $this->username = $val; // Direct assignment
    }

    public function setEmail(string $val) {
        $this->email = filter_var($val, FILTER_SANITIZE_EMAIL); // Sanitize email
    }

    public function setPassword(string $val) {
        $this->password = password_hash($val, PASSWORD_BCRYPT);
    }

    public function setRole(string $val) {
        $this->role = $val; // Direct assignment
    }

    public function setDateOfBirth(string $val) {
        $this->date_of_birth = $val; // Direct assignment
    }

    public function setCin(string $val) {
        $this->cin = $val; // Direct assignment
    }

    public function setPhoto(string $val) {
        $this->photo = $val; // Direct assignment
    }
    public function setNom(string $val) {
        $this->nom = $val; // Direct assignment
    }
    public function setPrenom(string $val) {
        $this->prenom = $val; // Direct assignment
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {  // Getter for email
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getDateOfBirth(): string {
        return $this->date_of_birth;
    }

    public function getCin(): string {
        return $this->cin;
    }

    public function getPhoto(): string {
        return $this->photo;
    }

    public function getNumTelephone(): string {
        return $this->numtelephone;
    }

    public function getNom(): string {
        return $this->nom;
    }
    public function getPrenom(): string {
        return $this->prenom;
    }

    // Other methods (like register, login, etc.) would go here as needed
}
?>
