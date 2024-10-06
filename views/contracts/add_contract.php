<?php
include '../../controllers/ContractController.php'; // Include the CarController

$carsController = new ContractController();
$errors = []; // Initialize an array to hold error messages

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get input values
    $user_id = trim($_POST['user_id']);
    $car_id = trim($_POST['car_id']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    // Validate input fields
    if (empty($user_id)) {
        $errors[] = "User ID is required.";
    }

    if (empty($car_id)) {
        $errors[] = "Car ID is required.";
    }

    if (empty($start_date)) {
        $errors[] = "Start date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $start_date)) {
        $errors[] = "Invalid start date format. Use YYYY-MM-DD.";
    }

    if (empty($end_date)) {
        $errors[] = "End date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $end_date)) {
        $errors[] = "Invalid end date format. Use YYYY-MM-DD.";
    } elseif ($start_date >= $end_date) {
        $errors[] = "End date must be after start date.";
    }

    // If there are no errors, proceed to add the contract
    if (empty($errors)) {
        // Create an instance of ContractController
        $contractController = new ContractController();
        try {
            $contractController->addContract($user_id, $car_id, $start_date, $end_date);
            // Redirect to the list of contracts after successful insertion
            header('Location: list_contracts.php');
            exit();
        } catch (Exception $e) {
            $errors[] = "Failed to add contract: " . $e->getMessage();
        }
    }
}

// Display errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>"; // Escape output for safety
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Contract</title>
</head>
<body>
    <h1>Add New Contract</h1>

    <!-- Form to add a new contract -->
    <form action="" method="POST">
        <label for="user_id">User ID:</label><br>
        <input type="text" name="user_id" id="user_id" required><br><br>

        <label for="car_id">Car ID:</label><br>
        <input type="text" name="car_id" id="car_id" required><br><br>

        <label for="start_date">Start Date (YYYY-MM-DD):</label><br>
        <input type="date" name="start_date" id="start_date" required><br><br>

        <label for="end_date">End Date (YYYY-MM-DD):</label><br>
        <input type="date" name="end_date" id="end_date" required><br><br>

        <input type="submit" name="submit" value="Add Contract">
    </form>
</body>
</html>
