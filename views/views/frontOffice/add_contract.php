<?php
include '../../controllers/ContractController.php'; // Include the ContractController

$contractController = new ContractController(); // Instance of ContractController

$errors = []; // Initialize an array to hold error messages

// Fetch available cars for the dropdown
$cars = $contractController->getAvailableCars(); // Assume this method returns an array of cars

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get input values
    $user_id = trim($_POST['user_id']);
    $car_title = trim($_POST['car_id']); // This will be the vehicle title
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    // Validate input fields
    if (empty($user_id)) {
        $errors[] = "User ID is required.";
    }

    if (empty($car_title)) {
        $errors[] = "Car title is required.";
    } else {
        // Convert the car title back to car ID for storage
        $car_id = $contractController->getCarIdByTitle($car_title); // Fetch car ID using title
        if (!$car_id) {
            $errors[] = "Invalid car selected.";
        }
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
        echo "<p style='color:red;'>" . $error . "</p>"; // Display error messages directly
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

        <label for="car_id">Car:</label><br>
        <select name="car_id" id="car_id" required>
            <option value="">Select a car</option>
            <?php foreach ($cars as $car): ?>
                <option value="<?php echo $car['vehicletitle']; ?>"><?php echo $car['vehicletitle']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="start_date">Start Date (YYYY-MM-DD):</label><br>
        <input type="date" name="start_date" id="start_date" required><br><br>

        <label for="end_date">End Date (YYYY-MM-DD):</label><br>
        <input type="date" name="end_date" id="end_date" required><br><br>

        <input type="submit" name="submit" value="Add Contract">
    </form>
</body>
</html>
