<?php
require_once '../../config/config.php';

class StatisticsController {

    // Fetch most rented cars by brand
    public function getMostRentedCarsByBrand() {
        $db = config::getConnexion();
        $sql = "
            SELECT cars.brand, COUNT(contracts.id) AS total_rented
            FROM contracts
            JOIN cars ON contracts.car_id = cars.id
            GROUP BY cars.brand
            ORDER BY total_rented DESC
        ";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Fetch most rented cars by fuel type
    public function getMostRentedCarsByFuelType() {
        $db = config::getConnexion();
        $sql = "
            SELECT cars.fueltype, COUNT(contracts.id) AS total_rented
            FROM contracts
            JOIN cars ON contracts.car_id = cars.id
            GROUP BY cars.fueltype
            ORDER BY total_rented DESC
        ";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Fetch total payments by year and month
    public function getTotalPaymentsByYearMonth() {
        $db = config::getConnexion();
        $sql = "
            SELECT DATE_FORMAT(start_date, '%Y-%m') AS payment_period, SUM(total_payment) AS total_payment
            FROM contracts
            GROUP BY DATE_FORMAT(start_date, '%Y-%m') 
            ORDER BY payment_period
        ";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log detailed error message for debugging
            error_log('Database error: ' . $e->getMessage());
            die('Error: ' . $e->getMessage());
        }
    }

    // Fetch top loyal clients
    public function getTopLoyalClients() {
        $db = config::getConnexion();
        $sql = "
            SELECT users.nom, users.prenom, COUNT(contracts.id) AS total_rented
            FROM contracts
            JOIN users ON contracts.user_id = users.id
            GROUP BY users.id
            ORDER BY total_rented DESC
            LIMIT 10
        ";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Fetch contracts by period
    public function getContractsByPeriod($period = 'month') {
        $db = config::getConnexion();
        $sql = "";
        
        // Group by month or year
        if ($period == 'month') {
            $sql = "
                SELECT DATE_FORMAT(start_date, '%Y-%m') AS period, COUNT(*) AS total_contracts
                FROM contracts
                GROUP BY period
                ORDER BY period ASC
            ";
        } elseif ($period == 'year') {
            $sql = "
                SELECT DATE_FORMAT(start_date, '%Y') AS period, COUNT(*) AS total_contracts
                FROM contracts
                GROUP BY period
                ORDER BY period ASC
            ";
        }
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Total number of cars
    public function getTotalCars() {
        $db = config::getConnexion();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM cars");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total']; // Ensure it returns an integer
    }

    // Total number of contracts
    public function getTotalContracts() {
        $db = config::getConnexion();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM contracts");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total']; // Ensure it returns an integer
    }

    // Total number of users
    public function getTotalUsers() {
        $db = config::getConnexion();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM users");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total']; // Ensure it returns an integer
    }

    // Total CA (Chiffre d'Affaires)
    public function getTotalCA() {
        $db = config::getConnexion();
        $sql = "
            SELECT end_date, SUM(total_payment) AS total_ca
            FROM contracts
            WHERE status = 'Completed'
            GROUP BY end_date
            ORDER BY end_date
        ";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total_ca']; // Ensure it returns a float
        } catch (PDOException $e) {
            // Log detailed error message for debugging
            error_log('Database error: ' . $e->getMessage());
            die('Error: ' . $e->getMessage());
        }
    }
    
    
}
?>
