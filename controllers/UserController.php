<?php
include '../../config/config.php';
include '../../models/User.php';

class UserController
{
    // List all users
    public function listUsers()
    {
        $sql = "SELECT * FROM users";
        $db = config::getConnexion();
        
        try {
            $list = $db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Filter users
    public function filterUsers($filters = [])
    {
        // Base query
        $sql = "SELECT * FROM users WHERE 1=1";
        $db = config::getConnexion();
        $params = [];

        // Apply filters
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['date_of_birth'])) {
            $sql .= " AND date_of_birth = :date_of_birth";
            $params[':date_of_birth'] = $filters['date_of_birth'];
        }


        $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Add new user
    public function addUser($username, $nom, $prenom, $email, $numtelephone, $password, $role, $date_of_birth, $cin, $photo)
    {
        $sql = "INSERT INTO users (username, nom, prenom, email, numtelephone, password, role, date_of_birth, cin, photo) 
                VALUES (:username, :nom, :prenom, :email, :numtelephone, :password, :role, :date_of_birth, :cin, :photo)";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => filter_var($email, FILTER_SANITIZE_EMAIL), // Sanitize email
                ':numtelephone' => $numtelephone,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':role' => $role,
                ':date_of_birth' => $date_of_birth,
                ':cin' => $cin,
                ':photo' => $photo
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Update user
    public function updateUser($id, $username, $nom, $prenom, $password, $email, $numtelephone, $role, $date_of_birth, $cin, $photo)
    {
        $sql = "UPDATE users SET username = :username, nom = :nom, prenom = :prenom, email = :email, numtelephone = :numtelephone, password = :password, role = :role, date_of_birth = :date_of_birth, cin = :cin, photo = :photo WHERE id = :id";
        $db = config::getConnexion();

        // Sanitize email separately
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $sanitizedEmail); // Pass the sanitized email variable
            $stmt->bindParam(':numtelephone', $numtelephone);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':date_of_birth', $date_of_birth);
            $stmt->bindParam(':cin', $cin);
            $stmt->bindParam(':photo', $photo);
            $stmt->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Delete a user
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Get user by ID
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Apply sorting to the SQL query
    public function applySorting($sql, $sortBy, $order)
    {
        $validSortBy = ['username', 'nom', 'prenom', 'role', 'date_of_birth', 'email']; // Added nom and prenom
        if (in_array($sortBy, $validSortBy)) {
            $order = ($order === 'desc') ? 'DESC' : 'ASC';
            $sql .= " ORDER BY " . $sortBy . " " . $order;
        }
        return $sql;
    }
}
?>
