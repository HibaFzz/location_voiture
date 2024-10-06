<?php
include '../../controllers/CarController.php';

$carsController = new CarController();

if (isset($_GET['id'])) {
    $carsController->deleteCar($_GET['id']);
    header(header: 'Location: list_cars.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
