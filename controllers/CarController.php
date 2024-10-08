<?php
include '../../config/config.php';
include '../../models/Car.php'; // Ensure you create this model

class CarController
{
    private $db;

    // Constructor accepting the database connection
    public function __construct() {
        $this->db = config::getConnexion(); // Use getConnexion to establish the database connection
    }


    // List all cars
    public function listCars() {
        $sql = "SELECT * FROM cars";
        
        try {
            $list = $this->db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC); // Fetch all results
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Filter cars based on various criteria
    public function getDistinctBrands()
    {
        return ["STAFIM", "TAMC", "Renault", "Peugeot", "Citroën", "Volkswagen", "Fiat", "Toyota", "Kia", "Hyundai", "Nissan", "BMW", "Mercedes-Benz", "Audi", "Dacia", "MG"];
    }

    // Method to fetch distinct fuel types
    public function getDistinctFuelTypes()
    {
        return ["diesel", "electric", "essence"];
    }

    // Method to filter cars based on criteria
   
    private function applySorting($sql, $sort_by, $order) {
        $validSortColumns = ['priceperday', 'modelyear', 'vehicletitle']; // Add valid column names
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC'; // Default to ASC
    
        if (!empty($sort_by) && in_array($sort_by, $validSortColumns)) {
            return $sql . " ORDER BY " . $sort_by . " " . $order;
        }
    
        return $sql; // Return original sql if no valid sort
    }
    
    public function filterCars($filters = [], $limit = 10, $offset = 0) {
        // Base query
        $sql = "SELECT * FROM cars";
        $params = []; // Array to hold the parameters for prepared statements
    
        // Check if all filters are empty
        $allFiltersEmpty = empty($filters['brand']) && 
                           empty($filters['disponible']) && 
                           empty($filters['fueltype']) && 
                           empty($filters['nbrpersonne']) && 
                           empty($filters['vehicletitle']) && 
                           empty($filters['modelyear']) && 
                           empty($filters['matricule']);
    
        if (!$allFiltersEmpty) {
            // If any filter is provided, apply the WHERE clause
            $sql .= " WHERE 1=1";
    
            // Add filters based on the criteria provided
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
    
            if (isset($filters['disponible']) && $filters['disponible'] !== '') {
                $sql .= " AND disponible = :disponible";
                $params[':disponible'] = $filters['disponible'] === 'oui' ? 1 : 0; // Map to 1 or 0
            }
    
            if (!empty($filters['fueltype'])) {
                $placeholders = [];
                foreach ($filters['fueltype'] as $key => $type) {
                    $placeholder = ":fueltype{$key}";
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $type;
                }
                $sql .= " AND fueltype IN (" . implode(', ', $placeholders) . ")";
            }
    
            if (!empty($filters['nbrpersonne'])) {
                $sql .= " AND nbrpersonne >= :nbrpersonne";
                $params[':nbrpersonne'] = $filters['nbrpersonne'];
            }
    
            if (!empty($filters['vehicletitle'])) {
                $sql .= " AND vehicletitle LIKE :vehicletitle";
                $params[':vehicletitle'] = "%" . $filters['vehicletitle'] . "%";
            }
    
            if (!empty($filters['modelyear'])) {
                $sql .= " AND modelyear = :modelyear";
                $params[':modelyear'] = $filters['modelyear'];
            }
    
            if (!empty($filters['matricule'])) {
                $sql .= " AND matricule LIKE :matricule";
                $params[':matricule'] = "%" . $filters['matricule'] . "%";
            }
        }
    
        // Apply sorting if required
        $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');
    
        // Add limit and offset
        $sql .= " LIMIT :limit OFFSET :offset";
    
        try {
            $stmt = $this->db->prepare($sql); // Prepare the SQL statement
    
            // Bind limit and offset as integers
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
            // Bind other parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
    
            $stmt->execute(); // Execute the prepared statement
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    // Make sure this method is properly handling the sorting logic
    
    public function getTotalCarsCount($filters = []) {
        // Start with the base query
        $sql = "SELECT COUNT(*) as total FROM cars"; // Fix the COUNT syntax
        $params = []; // Array to hold the parameters for prepared statements
    
        // Check if all filters are empty
        $allFiltersEmpty = empty($filters['brand']) && 
                           empty($filters['disponible']) && 
                           empty($filters['fueltype']) && 
                           empty($filters['nbrpersonne']) && 
                           empty($filters['vehicletitle']) && 
                           empty($filters['modelyear']) && 
                           empty($filters['matricule']);
        
        // Only add the WHERE clause if there are filters
        if (!$allFiltersEmpty) {
            $sql .= " WHERE 1=1"; // Add base WHERE clause
    
            // Add filters to the query
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
            if (isset($filters['disponible'])) {
                $sql .= " AND disponible = :disponible";
                $params[':disponible'] = $filters['disponible'] === 'oui' ? 1 : 0;
            }
            if (!empty($filters['fueltype'])) {
                $placeholders = [];
                foreach ($filters['fueltype'] as $key => $type) {
                    $placeholder = ":fueltype{$key}";
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $type;
                }
                $sql .= " AND fueltype IN (" . implode(', ', $placeholders) . ")";
            }
            if (!empty($filters['nbrpersonne'])) {
                $sql .= " AND nbrpersonne >= :nbrpersonne";
                $params[':nbrpersonne'] = $filters['nbrpersonne'];
            }
            if (!empty($filters['vehicletitle'])) {
                $sql .= " AND vehicletitle LIKE :vehicletitle";
                $params[':vehicletitle'] = '%' . $filters['vehicletitle'] . '%';
            }
            if (!empty($filters['modelyear'])) {
                $sql .= " AND modelyear = :modelyear";
                $params[':modelyear'] = $filters['modelyear'];
            }
            if (!empty($filters['matricule'])) {
                $sql .= " AND matricule LIKE :matricule";
                $params[':matricule'] = '%' . $filters['matricule'] . '%';
            }
        }
    
        // Prepare the statement
        $stmt = $this->db->prepare($sql);
    
        // Bind parameters if they exist
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    
        try {
            $stmt->execute(); // Execute the prepared statement
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
            return $result['total']; // Return the total count
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    


    // Get distinct brands from the cars
   /* public function getDistinctBrands() {
        $sql = "SELECT DISTINCT brand FROM cars";
        try {
            $stmt = $this->db->prepare($sql); // Use the class's db property
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch a single column (brand)
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
   

    // Fetch distinct fuel types
    public function getDistinctFuelTypes() {
        $sql = "SELECT DISTINCT fueltype FROM cars";
        try {
            $stmt = $this->db->prepare($sql); // Use the class's db property
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch a single column (fuel type)
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
*/
    // Add a new car
    public function addCar($matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible) {
        $sql = "INSERT INTO cars (matricule, image, vehicletitle, brand, vehicleoverview, priceperday, fueltype, modelyear, nbrpersonne, disponible) 
                VALUES (:matricule, :image, :vehicletitle, :brand, :vehicleoverview, :priceperday, :fueltype, :modelyear, :nbrpersonne, :disponible)";

        try {
            $query = $this->db->prepare($sql);
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

    // Apply sorting to the SQL query
  /*  public function applySorting($sql, $sortBy, $order) {
        // Define valid columns for sorting
        $validSortBy = ['vehicletitle', 'priceperday', 'nbrpersonne', 'modelyear'];
        
        // Ensure the sort column is valid; default to 'vehicletitle' if not
        $sortBy = in_array($sortBy, $validSortBy);
        
        // Check if the order is valid; default to 'ASC' if not
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';
    
        // Trim any trailing whitespace in $sql and append the ORDER BY clause
        return rtrim($sql) . " ORDER BY " . $sortBy . " " . $order;
    }
    
    */

    // Update an existing car// Update an existing car
     // Function to update car details
     public function updateCar($carId, $matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible) {
        try {
            $stmt = $this->db->prepare("
                UPDATE cars 
                SET 
                    matricule = :matricule,
                    image = :image,
                    vehicletitle = :vehicletitle,
                    brand = :brand,
                    vehicleoverview = :vehicleoverview,
                    priceperday = :priceperday,
                    fueltype = :fueltype,
                    modelyear = :modelyear,
                    nbrpersonne = :nbrpersonne,
                    disponible = :disponible
                WHERE id = :id
            ");

            // Binding parameters
            $stmt->bindParam(':matricule', $matricule);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':vehicletitle', $vehicletitle);
            $stmt->bindParam(':brand', $brand);
            $stmt->bindParam(':vehicleoverview', $vehicleoverview);
            $stmt->bindParam(':priceperday', $priceperday);
            $stmt->bindParam(':fueltype', $fueltype);
            $stmt->bindParam(':modelyear', $modelyear);
            $stmt->bindParam(':nbrpersonne', $nbrpersonne);
            $stmt->bindParam(':disponible', $disponible);
            $stmt->bindParam(':id', $carId);

            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handle exceptions
            error_log("Error updating car: " . $e->getMessage());
            return false;
        }
    }

    // Get a car by ID
    public function getCar($id) {
        $sql = "SELECT * FROM cars WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC); // Fetch single result
    }

    // Get the image path of a car by matricule
    public function getCarImagePathByMatricule($matricule) {
        $sql = "SELECT image_path FROM cars WHERE matricule = :matricule LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->bindParam(':matricule', $matricule); // Bind the matricule parameter
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC)['image_path'] ?? null; // Fetch single result and return image_path
    }

    // Delete a car
    public function deleteCar($id) {
        $sql = "DELETE FROM cars WHERE id = :id";
        $req = $this->db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
            echo "Car deleted successfully.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function updateCarAvailability($car_id, $availability) {
        // Validate availability input to ensure it's a boolean
        if (!is_bool($availability)) {
            throw new Exception("Invalid availability status. Use true (available) or false (not available).");
        }
    
        // Convert boolean to integer (1 or 0) for database storage
        $availabilityValue = $availability ? true : false;
    
        // SQL to update car availability
        $sql = "UPDATE cars SET disponible = :availability WHERE id = :car_id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':availability', $availabilityValue, PDO::PARAM_INT);
            $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
            $stmt->execute();
    
            echo "Car availability updated successfully.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    public function addContract($user_id, $car_id, $start_date, $end_date) {
        try {
            // Fetch car details for price per day
            $car = $this->getCar($car_id);

            if (!$car) {
                throw new Exception("Car not found");
            }

            // Calculate the number of days between start and end date
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $nbJours = $interval->days; // Total number of rental days

            // Ensure that the rental period is valid
            if ($nbJours <= 0) {
                throw new Exception("End date must be after start date.");
            }

            // Calculate total payment
            $totalPayment = $nbJours * $car['priceperday'];

            // Insert contract into database
            $sql = "INSERT INTO contracts (user_id, car_id, start_date, end_date, total_payment, status) 
                    VALUES (:user_id, :car_id, :start_date, :end_date, :total_payment, 'active')";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':car_id', $car_id);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':total_payment', $totalPayment);

            if (!$stmt->execute()) {
                throw new Exception("Failed to add contract: " . implode(", ", $stmt->errorInfo()));
            }

            // Update car availability to false (not available)
            if (!$this->updateCarAvailability($car_id, false)) {
                throw new Exception("Failed to update car availability.");
            }

            // Return success message with total payment
            return "Contract added successfully. Total payment: $$totalPayment.";
        } catch (Exception $e) {
            // Return error message instead of terminating the script
            return "Error: " . $e->getMessage();
        }
    }
    
    
   
}
