<?php
include '../../config/config.php';


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
    public function filterUsers($filters = [], $limit, $offset = 0)
{
    // Base query
    $sql = "SELECT * FROM users WHERE 1=1";
    $db = config::getConnexion();
    $params = [];

    // Apply filters
    if (!empty($filters['username'])) {
        $sql .= " AND username LIKE :username";
        $params[':username'] = "%" . $filters['username'] . "%";
    }
    if (!empty($filters['role'])) {
        $sql .= " AND role = :role";
        $params[':role'] = $filters['role'];
    }
    if (!empty($filters['nom'])) {
        $sql .= " AND nom LIKE :nom";
        $params[':nom'] = '%' . $filters['nom'] . '%';
    }
    if (!empty($filters['prenom'])) {
        $sql .= " AND prenom LIKE :prenom";
        $params[':prenom'] = '%' . $filters['prenom'] . '%';
    }
    if (!empty($filters['cin'])) {
        $sql .= " AND cin LIKE :cin";
        $params[':cin'] = "%" . $filters['cin'] . "%";
    }
    if (!empty($filters['date_of_birth'])) {
        $sql .= " AND date_of_birth = :date_of_birth";
        $params[':date_of_birth'] = $filters['date_of_birth'];
    }

    // Apply sorting
    $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');

    // Add pagination with LIMIT and OFFSET in the SQL query directly
    $sql .= " LIMIT :limit OFFSET :offset";

    try {
        $stmt = $db->prepare($sql);

        // Bind the limit and offset as integers for pagination
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Bind other filter parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

    public function getTotalUsers($filters = [])
{
    // Base query for counting users
    $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    $db = config::getConnexion();
    $params = [];

    // Apply filters
    if (!empty($filters['username'])) {
        $sql .= " AND username LIKE :username";
        $params[':username'] = "%" . $filters['username'] . "%";
    }
    if (!empty($filters['role'])) {
        $sql .= " AND role = :role";
        $params[':role'] = $filters['role'];
    }
    if (!empty($filters['nom'])) {
        $sql .= " AND nom LIKE :nom";
        $params[':nom'] = '%' . $filters['nom'] . '%';
    }
    if (!empty($filters['prenom'])) {
        $sql .= " AND prenom LIKE :prenom";
        $params[':prenom'] = '%' . $filters['prenom'] . '%';
    }
    if (!empty($filters['cin'])) {
        $sql .= " AND cin LIKE :cin";
        $params[':cin'] = "%" . $filters['cin'] . "%";
    }
    if (!empty($filters['date_of_birth'])) {
        $sql .= " AND date_of_birth = :date_of_birth";
        $params[':date_of_birth'] = $filters['date_of_birth'];
    }

    try {
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}


    // Add new user
     // Vérification si le CIN existe déjà
     public function cinExists($cin)
     {
         $sql = "SELECT COUNT(*) FROM users WHERE cin = :cin";
         $db = config::getConnexion();
 
         try {
             $stmt = $db->prepare($sql);
             $stmt->bindParam(':cin', $cin);
             $stmt->execute();
             return $stmt->fetchColumn() > 0; // Retourne vrai si le CIN existe
         } catch (Exception $e) {
             die('Error: ' . $e->getMessage());
         }
     }
 
     // Ajout d'un utilisateur
     public function addUser($username, $nom, $prenom, $email, $numtelephone, $password, $role, $date_of_birth, $cin, $photo)
     {
         // Vérification si le CIN est unique
         if ($this->cinExists($cin)) {
             throw new Exception("CIN already exists");
         }

        if ($this->usernameExists($username)) {
            throw new Exception("Username already exists");
        }
 
         $sql = "INSERT INTO users (username, nom, prenom, email, numtelephone, password, role, date_of_birth, cin, photo) 
                 VALUES (:username, :nom, :prenom, :email, :numtelephone, :password, :role, :date_of_birth, :cin, :photo)";
         $db = config::getConnexion();
 
         try {
             $stmt = $db->prepare($sql);
             $stmt->execute([
                 ':username' => $username,
                 ':nom'      => $nom,
                 ':prenom'   => $prenom,
                 ':email'    => filter_var($email, FILTER_SANITIZE_EMAIL), // Nettoyage de l'email
                 ':numtelephone' => $numtelephone,
                 ':password' => password_hash($password, PASSWORD_BCRYPT),
                 ':role'     => $role,
                 ':date_of_birth' => $date_of_birth,
                 ':cin'      => $cin,
                 ':photo'    => $photo
             ]);
         } catch (Exception $e) {
             throw new Exception($e->getMessage());
         }
     }

    // Update user
    public function updateUser($id, $username, $nom, $prenom, $password, $email, $numtelephone, $role, $date_of_birth, $cin, $photo)
    {
        // Vérification si le CIN est unique
        
        $sql = "UPDATE users SET username = :username, nom = :nom, prenom = :prenom, password = :password, email = :email, numtelephone = :numtelephone,  role = :role, date_of_birth = :date_of_birth, cin = :cin, photo = :photo WHERE id = :id";
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
    public function getUserByUsername($username)
{
    $sql = "SELECT * FROM users WHERE username = :username";
    $db = config::getConnexion();

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}
public function loginUser($username, $password)
  {
      $db = config::getConnexion();
      $sql = "SELECT * FROM users WHERE username = :username";
      
      try {
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':username', $username);
          $stmt->execute();
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
          if ($user && password_verify($password, $user['password'])) {
              // Set session data
              $_SESSION['user'] = [
                  'id' => $user['id'],
                  'username' => $user['username'],
                  'nom' => $user['nom'],
                  'prenom' => $user['prenom'],
                  'email' => $user['email'],
                  'numtelephone' => $user['numtelephone'],
                  'role' => $user['role'],
                  'date_of_birth' => $user['date_of_birth'],
                  'cin' => $user['cin'],
                  'photo' => $user['photo'], 
              ];
  
              // Redirect based on user role
              if ($user['role'] === 'admin') {
                  header('Location: ../backOffice/dashboard.php');
              } else if ($user['role'] === 'agent' || $user['role'] === 'client') {
                  header('Location: ../frontOffice/list_cars.php');
              }
              exit(); // Ensure the script stops after the redirection
          } else {
              return false; // Invalid credentials
          }
      } catch (Exception $e) {
          die('Error: ' . $e->getMessage());
      }
  }
  public function usernameExists($username)
  {
      $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
      $db = config::getConnexion();

      try {
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':username', $username);
          $stmt->execute();
          return $stmt->fetchColumn() > 0; // Retourne vrai si le username existe
      } catch (Exception $e) {
          die('Error: ' . $e->getMessage());
      }
  }

   
}

?>
