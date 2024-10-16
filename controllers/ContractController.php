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
            $nbJours = $interval->days;
    
            // Calculate total payment
            $totalPayment = $nbJours * $car['priceperday'];
            $currentDate = date('Y-m-d H:i:s'); // Date actuelle pour l'ajout du contrat
    
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
    
            // Update car availability to 'no' (not available)
            $carModel->updateCarAvailability($car_id, false);
            echo "Contract added successfully. Total payment: $totalPayment. Payment status: pending.";
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
            $stmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
            $stmt->execute();
    
            // Check if contract exists
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$contract) {
                throw new Exception("Contract not found or already canceled.");
            }
    
            $car_id = $contract['car_id'];
            $currentDate = date('Y-m-d H:i:s'); // Date actuelle pour l'annulation
    
            // SQL to update the contract status to 'canceled' and payment_status to 'refunded'
            $updateSql = "UPDATE contracts 
                          SET total_payment = NULL, status = 'canceled', date_canceled = :date_canceled, payment_status = 'refunded' 
                          WHERE id = :contract_id";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
            $updateStmt->bindParam(':date_canceled', $currentDate);
            $updateStmt->execute();
    
            // Update car availability to true (available)
            $carModel = new CarController();
            $carModel->updateCarAvailability($car_id, true);
    
            echo "Contract canceled successfully. Payment status: refunded.";
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

public function deleteContract($id) {
    $db = Config::getConnexion(); // Ensure Config is correctly capitalized
    $sql = "DELETE FROM contracts WHERE id = :id";
    
    // Correct the way you prepare the statement
    $req = $db->prepare($sql);
    
    $req->bindValue(':id', $id);

    try {
        $req->execute();
        echo "Contract deleted successfully.";
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
    public function updateContract($contract_id, $start_date, $end_date) {
        $db = config::getConnexion();
        try {
            // Fetch the contract
            $sql = "SELECT * FROM contracts WHERE id = :contract_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':contract_id', $contract_id);
            $stmt->execute();
            $contract = $stmt->fetch();
    
            if (!$contract) {
                throw new Exception("Contract not found");
            }
    
            // Fetch car details for price per day
            $carModel = new CarController();
            $car = $carModel->getCar($contract['car_id']);
            if (!$car) {
                throw new Exception("Car not found");
            }
    
            // Calculate the new number of days between start and end date
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $nbJours = $interval->days;
    
            // Calculate the new total payment
            $newTotalPayment = $nbJours * $car['priceperday'];
            $currentDate = date('Y-m-d H:i:s'); // Date actuelle pour la mise à jour
    
            // SQL to update contract with new dates and payment
            $sqlUpdate = "UPDATE contracts 
                          SET start_date = :start_date, end_date = :end_date, total_payment = :total_payment, date_updated = :date_updated 
                          WHERE id = :contract_id";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':start_date', $start_date);
            $stmtUpdate->bindParam(':end_date', $end_date);
            $stmtUpdate->bindParam(':total_payment', $newTotalPayment);
            $stmtUpdate->bindParam(':date_updated', $currentDate);
            $stmtUpdate->bindParam(':contract_id', $contract_id);
            $stmtUpdate->execute();
    
            
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
        $stmt = $db->prepare("SELECT nom, prenom, email , numtelephone ,cin FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarById($carId) {
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT vehicletitle, matricule, priceperday, fueltype , brand , modelyear FROM cars WHERE id = :id");
        $stmt->bindValue(':id', $carId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function filterCurrentContracts($filters = [], $limit = 10, $offset = 0)
{
    // Assuming you have a session variable storing the logged-in user's ID
    $currentUserId = $_SESSION['user']['id'];

    // Base query with JOINs to include user and car details, filtering for the current user's contracts
    $sql = "
        SELECT contracts.*, users.nom, users.prenom, cars.vehicletitle
        FROM contracts
        JOIN users ON contracts.user_id = users.id
        JOIN cars ON contracts.car_id = cars.id
        WHERE contracts.user_id = :currentUserId
    ";
    
    $db = config::getConnexion();
    $params = [':currentUserId' => $currentUserId];
    
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
    public function getTotalCurentContracts($filters = [])
{
    // Assuming you have a session variable storing the logged-in user's ID
    $currentUserId = $_SESSION['user']['id'];

    // Base query to count total contracts for the current user
    $sql = "
        SELECT COUNT(*) as total
        FROM contracts
        JOIN users ON contracts.user_id = users.id
        JOIN cars ON contracts.car_id = cars.id
        WHERE contracts.user_id = :currentUserId
    ";

    $db = config::getConnexion();
    $params = [':currentUserId' => $currentUserId];

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

public function markAsPaid($contract_id) {
    $db = config::getConnexion();
    try {
        // Check if the contract exists and is still pending payment
        $sql = "SELECT * FROM contracts WHERE id = :contract_id AND payment_status = 'pending'";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
        $stmt->execute();
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            throw new Exception("Contract not found or already paid.");
        }

        $currentDate = date('Y-m-d H:i:s'); // Date actuelle pour marquer le paiement

        // SQL to update payment_status to 'paid' and set the payment date
        $updateSql = "UPDATE contracts 
                      SET payment_status = 'paid', date_paid = :date_paid 
                      WHERE id = :contract_id";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->bindParam(':date_paid', $currentDate);
        $updateStmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
        $updateStmt->execute();

        header('Location: ../frontOffice/list_contracts.php');
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}
public function markAsCompleted($contract_id) {
    $db = config::getConnexion();
    try {
        // Récupérer les informations du contrat et vérifier s'il est actif
        $sql = "SELECT * FROM contracts WHERE id = :contract_id AND status = 'active'";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
        $stmt->execute();
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            throw new Exception("Contract not found or already completed.");
        }

        $car_id = $contract['car_id']; // Récupérer l'ID de la voiture associée au contrat
        $currentDate = date('Y-m-d H:i:s'); // Date actuelle pour marquer le contrat comme complété

        // Mettre à jour le statut du contrat à 'completed' et enregistrer la date de complétion
        $updateSql = "UPDATE contracts 
                      SET status = 'completed', date_updated = :date_updated 
                      WHERE id = :contract_id";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->bindParam(':date_updated', $currentDate);
        $updateStmt->bindParam(':contract_id', $contract_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Mettre à jour la disponibilité de la voiture à 'yes' (disponible)
        $carModel = new CarController();
        $carModel->updateCarAvailability($car_id, true);

        header('Location: ../frontOffice/list_contracts.php');
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}
public function getUserContracts($user_id) {
    $db = config::getConnexion();
    try {
        // SQL query to fetch contracts for the current user
        $sql = "SELECT contracts.id, cars.vehicletitle, contracts.start_date, contracts.end_date, 
                       contracts.total_payment, contracts.status, contracts.payment_status, contracts.date_paid 
                FROM contracts 
                JOIN cars ON contracts.car_id = cars.id 
                WHERE contracts.user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return contracts data
        return $contracts;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}
public function getRecentlyReturnedCarsLast7Days($page = 1, $limit = 10) {
    $db = config::getConnexion();
    try {
        // Calculate the offset
        $offset = ($page - 1) * $limit;

        // SQL to count the total number of records for pagination
        $countSql = "SELECT COUNT(*) as total
                     FROM cars c
                     JOIN contracts co ON c.id = co.car_id
                     WHERE co.status IN ('completed', 'canceled')
                     AND (co.date_updated >= NOW() - INTERVAL 7 DAY OR co.date_canceled >= NOW() - INTERVAL 7 DAY)";

        $stmtCount = $db->prepare($countSql);
        $stmtCount->execute();
        $totalRecords = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Calculate total pages
        $totalPages = ceil($totalRecords / $limit);

        // SQL to get paginated results for cars returned in the last 7 days
        $sql = "SELECT c.id AS car_id, c.matricule, c.vehicletitle, co.status, co.date_updated, co.date_canceled, u.username
                FROM cars c
                JOIN contracts co ON c.id = co.car_id
                JOIN users u ON co.user_id = u.id
                WHERE co.status IN ('completed', 'canceled')
                AND (co.date_updated >= NOW() - INTERVAL 7 DAY OR co.date_canceled >= NOW() - INTERVAL 7 DAY)
                ORDER BY GREATEST(co.date_updated, co.date_canceled) DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch all returned cars
        $returnedCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'cars' => $returnedCars,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ];
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

}


?>
