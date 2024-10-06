
?>
<?php
class Contract {
    private $conn;
    public $id;
    public $user_id;
    public $car_id;
    public $start_date;
    public $end_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO contracts (user_id, car_id, start_date, end_date, status) VALUES (:user_id, :car_id, :start_date, :end_date, :status)";
        $stmt = $this->conn->prepare($query);
        // Sanitize
        $this->user_id = htmlspecialchars(string: strip_tags(string: $this->user_id));
        $this->car_id = htmlspecialchars(string: strip_tags(string: $this->car_id));
        $this->start_date = htmlspecialchars(string: strip_tags(string: $this->start_date));
        $this->end_date = htmlspecialchars(string: strip_tags(string: $this->end_date));
        $this->status = htmlspecialchars(string: strip_tags(string: $this->status));

        // Bind
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        return $stmt->execute() ? true : false;
    }

    public function getAllContracts(): mixed {
        $query = "SELECT * FROM contracts";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContractById($id): mixed {
        $query = "SELECT * FROM contracts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateContract() {
        $query = "UPDATE contracts SET user_id = :user_id, car_id = :car_id, start_date = :start_date, end_date = :end_date, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        // Sanitize
        $this->user_id = htmlspecialchars(string: strip_tags(string: $this->user_id));
        $this->car_id = htmlspecialchars(string: strip_tags(string: $this->car_id));
        $this->start_date = htmlspecialchars(string: strip_tags(string: $this->start_date));
        $this->end_date = htmlspecialchars(string: strip_tags(string: $this->end_date));
        $this->status = htmlspecialchars(string: strip_tags(string: $this->status));

        // Bind
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute() ? true : false;
    }

    public function deleteContract($id): bool {
        $query = "DELETE FROM contracts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() ? true : false;
    }

    // Function to update stock based on contract status
    public function updateStock(): void {
        if ($this->status == 'terminated' || $this->status == 'canceled') {
            // Increment stock
            $query = "UPDATE cars SET stock = stock + 1 WHERE id = :car_id";
        } else {
            // Decrement stock
            $query = "UPDATE cars SET stock = stock - 1 WHERE id = :car_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();
    }
}
?>
