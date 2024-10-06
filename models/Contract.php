<?php
class Contract {
    private $conn;
    private $id;
    private $user_id;
    private $car_id;
    private $start_date;
    private $end_date;
    private $status;
    private $total_payment;  // New property for total payment

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getter and Setter for ID
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // Getter and Setter for User ID
    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    // Getter and Setter for Car ID
    public function getCarId() {
        return $this->car_id;
    }

    public function setCarId($car_id) {
        $this->car_id = $car_id;
    }

    // Getter and Setter for Start Date
    public function getStartDate() {
        return $this->start_date;
    }

    public function setStartDate($start_date) {
        $this->start_date = $start_date;
    }

    // Getter and Setter for End Date
    public function getEndDate() {
        return $this->end_date;
    }

    public function setEndDate($end_date) {
        $this->end_date = $end_date;
    }

    // Getter and Setter for Status
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // Getter and Setter for Total Payment
    public function getTotalPayment() {
        return $this->total_payment;
    }

    public function setTotalPayment($total_payment) {
        $this->total_payment = $total_payment;
    }

    // Function to calculate total payment based on rental duration and daily rate
   /* public function calculateTotalPayment($daily_rate) {
        $start = new DateTime($this->start_date);
        $end = new DateTime($this->end_date);
        $interval = $start->diff($end);
        $days = $interval->days;
        $this->total_payment = $days * $daily_rate;
    }*/
}
?>
