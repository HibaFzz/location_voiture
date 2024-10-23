<?php
include '../../controllers/UserController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['admin']);
$userController = new UserController();

if (isset($_GET['id'])) {
    $userController->deleteUser($_GET['id']);
    header(header: 'Location: user_list.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
