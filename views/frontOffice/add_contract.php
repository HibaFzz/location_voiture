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
        $errors[] = "End date must be after the start date.";
    }

    // If no errors, proceed to add the contract
    if (empty($errors)) {
        $contractController->addContract($user_id, $car_id, $start_date, $end_date);

        // Redirect to the list of contracts after successful insertion
        header('Location: list_contracts.php');
        exit();
    }
}

// Display errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>" . $error . "</p>"; // Output error message directly
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Contract</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-size: 2em;
            margin-bottom: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px;
        }

        .form-group {
            display: flex;
            margin-bottom: 15px;
        }

        .form-group label {
            flex-basis: 35%;
            margin-right: 10px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
            flex-basis: 60%;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        .return-link {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
        }

        .return-link a {
            color: #007bff;
            text-decoration: none;
        }

        .return-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-group {
                flex-direction: column;
            }

            .form-group label {
                flex-basis: auto;
                margin-bottom: 5px;
            }

            .form-group input[type="text"],
            .form-group input[type="date"],
            .form-group select {
                flex-basis: 100%;
            }

            input[type="submit"] {
                width: auto;
            }
        }
    </style>
</head>
<body>
    <h2>Add A New Contract</h2>

    <!-- Display errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to add a new contract -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="user_id">User ID:</label>
            <input type="text" name="user_id" id="user_id" required>
        </div>

        <div class="form-group">
            <label for="car_id">Car:</label>
            <select name="car_id" id="car_id" required>
                <option value="">Select Car</option>
                <?php foreach ($cars as $car): ?>
                    <option value="<?php echo $car['vehicletitle']; ?>"><?php echo $car['vehicletitle']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date (YYYY-MM-DD):</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date (YYYY-MM-DD):</label>
            <input type="date" name="end_date" id="end_date" required>
        </div>

        <input type="submit" name="submit" value="Add Contract">
    </form>

    <!-- Return to list of contracts link -->
    <div class="return-link">
        <a href="list_contracts.php">Return to List Contracts</a>
    </div>
</body>
</html>
