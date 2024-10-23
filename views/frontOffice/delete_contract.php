<?php
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);
$carsController = new ContractController();

if (isset($_GET['id'])) {
    $carsController->DeleteContract($_GET['id']);
    header(header: 'Location: list_contracts.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
