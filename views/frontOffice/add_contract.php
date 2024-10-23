<?php
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['agent']);
$contractController = new ContractController();
$currentUser = AuthController::getCurrentUser();

$errors = []; 

$cars = $contractController->getAvailableCars();

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $car_title = trim($_POST['car_id']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } else {
        $user = $contractController->getUserByUsername($username);
        if (!$user) {
            $errors['username'] = "Invalid username.";
        } else {
            $user_id = $user['id'];
        }
    }

    if (empty($car_title)) {
        $errors['car_id'] = "Car selection is required.";
    } else {
        $car_id = $contractController->getCarIdByTitle($car_title);
        if (!$car_id) {
            $errors['car_id'] = "Invalid car selected.";
        }
    }

    if (empty($start_date)) {
        $errors['start_date'] = "Start date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $start_date)) {
        $errors['start_date'] = "Invalid start date format. Use YYYY-MM-DD.";
    } else {
        $currentDate = new DateTime(); 
        $inputStartDate = new DateTime($start_date);
        
        if ($inputStartDate < $currentDate) {
            $errors['start_date'] = "Start date must be greater than or equal to today.";
        }
    }
    

    if (empty($end_date)) {
        $errors['end_date'] = "End date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $end_date)) {
        $errors['end_date'] = "Invalid end date format. Use YYYY-MM-DD.";
    } elseif ($start_date >= $end_date) {
        $errors['end_date'] = "End date must be after the start date.";
    }

    if (empty($errors)) {
        $contractController->addContract($user_id, $car_id, $start_date, $end_date);
        header('Location: list_contracts.php');
        exit();
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
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
        }

        .error {
            color: red;
            font-size: 0.875em;
            margin-top: 5px;
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
<div style="padding-top: 100px;"></div>
    <h2>Add A New Contract</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
            <?php if (!empty($errors['username'])): ?>
                <div class="error"><?php echo $errors['username']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="car_id">Car:</label>
            <select name="car_id" id="car_id">
                <option value="">Select Car</option>
                <?php foreach ($cars as $car): ?>
                    <option value="<?php echo $car['vehicletitle']; ?>" <?php echo (isset($_POST['car_id']) && $_POST['car_id'] == $car['vehicletitle']) ? 'selected' : ''; ?>>
                        <?php echo $car['vehicletitle']; ?>
                        <?php echo $car['matricule'];?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['car_id'])): ?>
                <div class="error"><?php echo $errors['car_id']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date (YYYY-MM-DD):</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>">
            <?php if (!empty($errors['start_date'])): ?>
                <div class="error"><?php echo $errors['start_date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="end_date">End Date (YYYY-MM-DD):</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
            <?php if (!empty($errors['end_date'])): ?>
                <div class="error"><?php echo $errors['end_date']; ?></div>
            <?php endif; ?>
        </div>

        <input type="submit" name="submit" value="Add Contract">
    </form>

    <div class="return-link">
        <a href="list_contracts.php">Return to List Contracts</a>
    </div>
</body>
</html>
