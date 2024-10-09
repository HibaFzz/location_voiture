<?php
include '../../controllers/ContractController.php';

$carsController = new ContractController();

if (isset($_GET['id'])) {
    $carsController->DeleteContract($_GET['id']);
    header(header: 'Location: list_contracts.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
