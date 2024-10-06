<?php
include 'CarController.php';

class ContractController
{
    
    // Add Contract with payment calculation
    public function addContract($user_id, $car_id, $start_date, $end_date) {
        $db = config::getConnexion();
        try {
            // Fetch car details for price per day
            $carModel = new CarController();
            $car = $carModel->getCar($car_id);
    
            if (!$car) {
                throw new Exception("Car not found");
            }
    
            // Calculate the number of days between start and end date
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $nbJours = $interval->days; // Total number of rental days
    
            // Calculate total payment
            $totalPayment = $nbJours * $car['priceperday'];
    
            // SQL to insert contract
            $sql = "INSERT INTO contracts (user_id, car_id, start_date, end_date, total_payment, status) 
                    VALUES (:user_id, :car_id, :start_date, :end_date, :total_payment, 'active')";
    
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':car_id', $car_id);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':total_payment', $totalPayment);
            $stmt->execute();
    
            // Update car availability to 'no' (not available)
            $carModel->updateCarAvailability($car_id, 'no');
            echo "Contract added successfully. Total payment: $totalPayment.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function cancelContract($contract_id) {
        $db = config::getConnexion();
        try {
            // SQL to fetch the car ID associated with the contract
            $sql = "SELECT car_id FROM contracts WHERE id = :contract_id AND status = 'active'";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':contract_id', $contract_id);
            $stmt->execute();
            
            // Check if contract exists
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$contract) {
                throw new Exception("Contract not found or already canceled.");
            }
    
            $car_id = $contract['car_id'];
    
            // SQL to update the contract status and remove total payment
            $updateSql = "UPDATE contracts 
                          SET total_payment = NULL, status = 'canceled' 
                          WHERE id = :contract_id";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->bindParam(':contract_id', $contract_id);
            $updateStmt->execute();
    
            // Update car availability to 'yes' (available)
            $carModel = new CarController();
            $carModel->updateCarAvailability($car_id, 'yes');
    
            echo "Contract canceled successfully. Car availability updated.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    

    // List all contracts
    public function listContracts()
{
    $db = config::getConnexion();
    $sql = "
        SELECT contracts.*, users.nom, users.prenom, cars.vehicletitle
        FROM contracts
        JOIN users ON contracts.user_id = users.id
        JOIN cars ON contracts.car_id = cars.id
    ";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}


    // Get contract by ID
    public function getContractById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM contracts WHERE id = :id";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Update a contract's status
    public function updateContractStatus($id, $status)
    {
        $db = config::getConnexion();
        $sql = "UPDATE contracts SET status = :status WHERE id = :id";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            echo "Contract status updated successfully.";
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Filter Contracts with specified criteria
    public function filterContracts($filters = [], $limit = 10, $offset = 0)
{
    // Base query with JOINs to include user and car details
    $sql = "
        SELECT contracts.*, users.nom, users.prenom, cars.vehicletitle
        FROM contracts
        JOIN users ON contracts.user_id = users.id
        JOIN cars ON contracts.car_id = cars.id
        WHERE 1=1
    ";
    
    $db = config::getConnexion();
    $params = [];
    
    // Filter based on status
    if (isset($filters['status']) && $filters['status'] !== '') {
        $sql .= " AND contracts.status = :status";
        $params[':status'] = $filters['status'];
    }

    // Filter based on start_date
    if (!empty($filters['start_date'])) {
        $sql .= " AND contracts.start_date >= :start_date";
        $params[':start_date'] = $filters['start_date'];
    }

    // Filter based on end_date
    if (!empty($filters['end_date'])) {
        $sql .= " AND contracts.end_date <= :end_date";
        $params[':end_date'] = $filters['end_date'];
    }

    // Filter by user name (both first name and last name)
    if (!empty($filters['user_name'])) {
        $sql .= " AND (users.nom LIKE :user_name OR users.prenom LIKE :user_name)";
        $params[':user_name'] = '%' . $filters['user_name'] . '%';
    }

    // Filter by vehicle title
    if (!empty($filters['vehicletitle'])) {
        $sql .= " AND cars.vehicletitle LIKE :vehicletitle";
        $params[':vehicletitle'] = '%' . $filters['vehicletitle'] . '%';
    }

    // Apply sorting if required
    $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');

    // Add pagination (LIMIT and OFFSET)
    $sql .= " LIMIT :limit OFFSET :offset";

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
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
public function getTotalContracts($filters = [])
{
    // Base query to count total contracts with filters applied
    $sql = "
        SELECT COUNT(*) as total
        FROM contracts
        JOIN users ON contracts.user_id = users.id
        JOIN cars ON contracts.car_id = cars.id
        WHERE 1=1
    ";

    $db = config::getConnexion();
    $params = [];

    // Apply the same filters as in the `filterContracts` method
    if (isset($filters['status']) && $filters['status'] !== '') {
        $sql .= " AND contracts.status = :status";
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['start_date'])) {
        $sql .= " AND contracts.start_date >= :start_date";
        $params[':start_date'] = $filters['start_date'];
    }

    if (!empty($filters['end_date'])) {
        $sql .= " AND contracts.end_date <= :end_date";
        $params[':end_date'] = $filters['end_date'];
    }

    if (!empty($filters['user_name'])) {
        $sql .= " AND (users.nom LIKE :user_name OR users.prenom LIKE :user_name)";
        $params[':user_name'] = '%' . $filters['user_name'] . '%';
    }

    if (!empty($filters['vehicletitle'])) {
        $sql .= " AND cars.vehicletitle LIKE :vehicletitle";
        $params[':vehicletitle'] = '%' . $filters['vehicletitle'] . '%';
    }

    try {
        $stmt = $db->prepare($sql);
        
        // Bind filter parameters
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

    
    // Apply sorting to the SQL query
    public function applySorting($sql, $sortBy, $order)
    {
        // Validate sort column
        $validSortBy = ['start_date', 'end_date', 'total_payment'];
        if (in_array($sortBy, $validSortBy)) {
            $order = ($order === 'desc') ? 'DESC' : 'ASC'; // Default to ascending
            $sql .= " ORDER BY " . $sortBy . " " . $order;
        }
        return $sql;
    }
    public function getAvailableCars() {
        $db = config::getConnexion(); // Database connection
        // Assuming a database connection is already established
        $stmt = $db->prepare("SELECT id, vehicletitle FROM cars WHERE disponible = 1"); // Removed the $this
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
    }
    
    public function getCarIdByTitle($title) {
        $db = config::getConnexion(); // Database connection
        // Fetch the car ID based on the title
        $stmt = $db->prepare("SELECT id FROM cars WHERE vehicletitle = :vehicletitle"); // Removed the $this
        $stmt->execute([':vehicletitle' => $title]);
        return $stmt->fetchColumn(); // Fetch the ID or false if not found
    }
    public function getUserById($userId) {
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT nom, prenom, email FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarById($carId) {
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT vehicletitle, matricule, priceperday, fueltype FROM cars WHERE id = :id");
        $stmt->bindValue(':id', $carId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
