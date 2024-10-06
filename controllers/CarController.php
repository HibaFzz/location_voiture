<?php
include '../../config/config.php';
include '../../models/Car.php'; // Make sure you create this model
class CarController
{
    // List all cars
    public function listCars()
    {
        $sql = "SELECT * FROM cars";
        $db = config::getConnexion();
        
        try {
            $list = $db->query($sql);
            return $list->fetchAll(); // Fetch all results
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    public function filterCars($filters = []) {
        // Base query
        $sql = "SELECT * FROM cars WHERE 1=1";
        $db = config::getConnexion(); // Assuming this gets the database connection
        $params = []; // Array to hold the parameters for prepared statements
    
        // Add filters based on the criteria provided
        if (!empty($filters['brand'])) {
            $sql .= " AND brand = :brand";
            $params[':brand'] = $filters['brand'];
        }
        if (isset($filters['disponible']) && $filters['disponible'] !== '') {
            $sql .= " AND disponible = :disponible";
            $params[':disponible'] = $filters['disponible'];
        }
        if (!empty($filters['fueltype'])) {
            $sql .= " AND fueltype = :fueltype";
            $params[':fueltype'] = $filters['fueltype'];
        }
        if (!empty($filters['nbrpersonne'])) {
            $sql .= " AND nbrpersonne >= :nbrpersonne";
            $params[':nbrpersonne'] = $filters['nbrpersonne'];
        }
        if (!empty($filters['vehicletitle'])) {
            $sql .= " AND vehicletitle LIKE :vehicletitle";
            $params[':vehicletitle'] = "%" . $filters['vehicletitle'] . "%";  // Using LIKE for partial matches
        }
        if (!empty($filters['modelyear'])) {
            $sql .= " AND modelyear = :modelyear";
            $params[':modelyear'] = $filters['modelyear'];
        }
        if (!empty($filters['matricule'])) {
            $sql .= " AND matricule LIKE :matricule";
            $params[':matricule'] = "%" . $filters['matricule'] . "%";  // Using LIKE for partial matches
        }
    
        // Apply sorting if required
        $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');
        
        try {
            $stmt = $db->prepare($sql); // Prepare the SQL statement
            $stmt->execute($params); // Execute with bound parameters
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    

    
    public function getDistinctBrands() {
        $db = config::getConnexion(); // Assuming this gets the database connection
        $sql = "SELECT DISTINCT brand FROM cars";
        try {
            $stmt = $db->prepare($sql); // Correctly reference the database connection
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch a single column (brand)
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Fetch distinct fuel types
    public function getDistinctFuelTypes() {
        $db = config::getConnexion(); // Assuming this gets the database connection
        $sql = "SELECT DISTINCT fueltype FROM cars";
        try {
            $stmt = $db->prepare($sql); // Correctly reference the database connection
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch a single column (fuel type)
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Add Car
    public function addCar($matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible)
    {
        $sql = "INSERT INTO cars (matricule, image, vehicletitle, brand, vehicleoverview, priceperday, fueltype, modelyear, nbrpersonne, disponible) 
                VALUES (:matricule, :image, :vehicletitle, :brand, :vehicleoverview, :priceperday, :fueltype, :modelyear, :nbrpersonne, :disponible)";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':matricule' => $matricule,
                ':image' => $image,
                ':vehicletitle' => $vehicletitle,
                ':brand' => $brand,
                ':vehicleoverview' => $vehicleoverview,
                ':priceperday' => $priceperday,
                ':fueltype' => $fueltype,
                ':modelyear' => $modelyear,
                ':nbrpersonne' => $nbrpersonne,
                ':disponible' => $disponible // Add disponible value
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function applySorting($sql, $sortBy, $order) {
        // Validate sort column
        $validSortBy = ['vehicletitle', 'priceperday', 'nbrpersonne'];
        if (in_array($sortBy, $validSortBy)) {
            $order = ($order === 'desc') ? 'DESC' : 'ASC';  // Default to ascending
            $sql .= " ORDER BY " . $sortBy . " " . $order;
        }
        return $sql;
    }

    // Update an existing car
    public function updateCar($id, $matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible)
    {
        $sql = "UPDATE cars SET matricule = :matricule, image = :image, vehicletitle = :vehicletitle, brand = :brand, 
                vehicleoverview = :vehicleoverview, priceperday = :priceperday, fueltype = :fueltype, modelyear = :modelyear, nbrpersonne = :nbrpersonne, disponible = :disponible 
                WHERE id = :id";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id', $id);
            $query->bindParam(':matricule', $matricule);
            $query->bindParam(':image', $image);
            $query->bindParam(':vehicletitle', $vehicletitle);
            $query->bindParam(':brand', $brand);
            $query->bindParam(':vehicleoverview', $vehicleoverview);
            $query->bindParam(':priceperday', $priceperday);
            $query->bindParam(':fueltype', $fueltype);
            $query->bindParam(':modelyear', $modelyear);
            $query->bindParam(':nbrpersonne', $nbrpersonne);
            $query->bindParam(':disponible', $disponible); // Bind disponible
            $query->execute();
            echo $query->rowCount() . " records updated successfully";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    // Get a car by ID
    public function getCar($id)
    {
        $sql = "SELECT * FROM cars WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC); // Fetch single result
    }
    
    public function getCarImagePathByMatricule($matricule)
    {
        $sql = "SELECT image_path FROM cars WHERE matricule = :matricule LIMIT 1";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindParam(':matricule', $matricule); // Bind the matricule parameter
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC)['image_path'] ?? null; // Fetch single result and return image_path
    }
    

    

    // Update an existing car
    
    // Delete a car
    public function deleteCar($id)
    {
        $sql = "DELETE FROM cars WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
            echo "Car deleted successfully.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Get a car by ID

}
