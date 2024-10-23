<?php
include '../../config/config.php';
include '../../models/Car.php'; 

class CarController
{
    private $db;


    public function __construct() {
        $this->db = config::getConnexion(); 
    }



    public function listCars() {
        $sql = "SELECT * FROM cars";
        
        try {
            $list = $this->db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC); // Fetch all results
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getDistinctBrands()
    {
        return ["STAFIM", "TAMC", "Renault", "Peugeot", "Citroën", "Volkswagen", "Fiat", "Toyota", "Kia", "Hyundai", "Nissan", "BMW", "Mercedes-Benz", "Audi", "Dacia", "MG"];
    }

    // Method to fetch distinct fuel types
    public function getDistinctFuelTypes()
    {
        return ["diesel", "electric", "essence"];
    }

   
    private function applySorting($sql, $sort_by, $order) {
        $validSortColumns = ['priceperday', 'modelyear', 'vehicletitle']; 
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC'; 
    
        if (!empty($sort_by) && in_array($sort_by, $validSortColumns)) {
            return $sql . " ORDER BY " . $sort_by . " " . $order;
        }
    
        return $sql; 
    }
    
    public function filterCars($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM cars";
        $params = [];
    
        // Check if all filters are empty
        $allFiltersEmpty = empty($filters['brand']) && 
                           empty($filters['disponible']) && 
                           (empty($filters['fueltype']) || $filters['fueltype'] === ['']) &&  // Check if fueltype is an empty array or contains an empty string
                           empty($filters['nbrpersonne']) && 
                           empty($filters['vehicletitle']) && 
                           empty($filters['modelyear']) && 
                           empty($filters['matricule']);
        
        // Apply filters only if not all filters are empty
        if (!$allFiltersEmpty) {
            $sql .= " WHERE 1=1"; // Always true condition to append other conditions
    
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
    
            if (isset($filters['disponible']) && $filters['disponible'] !== '') {
                $sql .= " AND disponible = :disponible";
                $params[':disponible'] = ($filters['disponible'] === 'oui') ? 1 : 0;
            }
    
            if (!empty($filters['fueltype']) && $filters['fueltype'] !== ['']) {
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
    
        // Sorting and pagination
        $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');
        $sql .= " LIMIT :limit OFFSET :offset";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
            // Bind other parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    public function getTotalCarsCount($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM cars";
        $params = [];
    
        $allFiltersEmpty = empty($filters['brand']) && 
                           empty($filters['disponible']) && 
                           empty($filters['fueltype']) && 
                           empty($filters['nbrpersonne']) && 
                           empty($filters['vehicletitle']) && 
                           empty($filters['modelyear']) && 
                           empty($filters['matricule']);
    
        if (!$allFiltersEmpty) {
            $sql .= " WHERE 1=1";
    
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
    
            if (isset($filters['disponible']) && $filters['disponible'] !== '') {
                $sql .= " AND disponible = :disponible";
                $params[':disponible'] = ($filters['disponible'] === 'oui') ? 1 : 0;
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
    
        try {
            $stmt = $this->db->prepare($sql);
    
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
    
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
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
    }     public function updateCar($carId, $matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible) {
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
            error_log("Error updating car: " . $e->getMessage());
            return false;
        }
    }

    public function getCar($id) {
        $sql = "SELECT * FROM cars WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC); 
    }

    public function getCarImagePathByMatricule($matricule) {
        $sql = "SELECT image FROM cars WHERE matricule = :matricule LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->bindParam(':matricule', $matricule); 
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC)['image'] ?? null; 
    }

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
        if (!is_bool($availability)) {
            throw new Exception("Invalid availability status. Use true (available) or false (not available).");
        }
    
        $availabilityValue = $availability ? true : false;
    
        $sql = "UPDATE cars SET disponible = :availability WHERE id = :car_id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':availability', $availabilityValue, PDO::PARAM_INT);
            $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
            $stmt->execute();
    
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    public function addContract($user_id, $car_id, $start_date, $end_date): void {
    $db = config::getConnexion();
    
    try {
        // Fetch car details for price per day
        $carModel = new CarController();
        $car = $carModel->getCar($car_id);

        if (!$car) {
            // Log error and exit without returning
            error_log("Car with ID $car_id not found.");
            return; // Exit function early if car not found
        }

        // Calculate the number of days between start and end date
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        $nbJours = $interval->days;

        // Calculate total payment
        $totalPayment = $nbJours * $car['priceperday'];
        $currentDate = date('Y-m-d H:i:s'); // Current date for adding contract

        // SQL to insert contract with payment_status as 'pending'
        $sql = "INSERT INTO contracts (user_id, car_id, start_date, end_date, total_payment, status, date_added, payment_status) 
                VALUES (:user_id, :car_id, :start_date, :end_date, :total_payment, 'active', :date_added, 'pending')";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':car_id', $car_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':total_payment', $totalPayment);
        $stmt->bindParam(':date_added', $currentDate);
        $stmt->execute();

        // Update car availability
        $carModel->updateCarAvailability($car_id, false);

    } catch (Exception $e) {
        // Log error instead of halting the script
        error_log('Error adding contract: ' . $e->getMessage());
        // No return statement here, as this is a void function
    }
}

    
    
   
}
