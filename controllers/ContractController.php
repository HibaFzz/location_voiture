<?php
include '../../controllers/CarController.php';

class ContractController
{
    
    // Add Contract with payment calculation
    public function addContract($user_id, $car_id, $start_date, $end_date)
    {
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

    // List all contracts
    public function listContracts()
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM contracts";
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
    public function filterContracts($filters = [])
    {
        // Base query
        $sql = "SELECT * FROM contracts WHERE 1=1"; // Default query

        $db = config::getConnexion(); // Database connection
        $params = []; // Array to hold parameters for prepared statements

        // Filter based on status
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filter based on start_date
        if (!empty($filters['start_date'])) {
            $sql .= " AND start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        // Filter based on end_date
        if (!empty($filters['end_date'])) {
            $sql .= " AND end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Search by start_date, end_date, total_payment with LIKE
        if (!empty($filters['search'])) {
            $sql .= " AND (start_date LIKE :search OR end_date LIKE :search OR total_payment LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%'; // Using LIKE for partial matches
        }

        // Apply sorting if required
        $sql = $this->applySorting($sql, $filters['sort_by'] ?? '', $filters['order'] ?? 'asc');

        try {
            $stmt = $db->prepare($sql); // Prepare the SQL statement
            $stmt->execute($params); // Execute with bound parameters
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
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
}
?>
