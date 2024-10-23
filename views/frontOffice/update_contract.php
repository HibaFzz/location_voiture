<?php
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

$contractController = new ContractController(); 
$errors = []; 

if (isset($_GET['id'])) {
    $contractId = (int)$_GET['id']; 

    try {
        $contract = $contractController->getContractById($contractId);
        if (!$contract) {
            $errors[] = "Contract not found.";
        }
    } catch (Exception $e) {
        $errors[] = "Error fetching contract: " . $e->getMessage();
    }
} else {
    $errors[] = "No contract ID provided.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($contract)) {

    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    if (empty($start_date)) {
        $errors['start_date'] = "Start date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $start_date)) {
        $errors['start_date'] = "Invalid start date format. Use YYYY-MM-DD.";
    } else {
        // Check if start_date is greater than or equal to the current date
        $currentDate = new DateTime(); // Get current date
        $inputStartDate = new DateTime($start_date); // Convert input to DateTime object
        
        if ($inputStartDate < $currentDate) {
            $errors['start_date'] = "Start date must be greater than or equal to today.";
        }
    }
    

    if (empty($end_date)) {
        $errors[] = "End date is required.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $end_date)) {
        $errors[] = "Invalid end date format. Use YYYY-MM-DD.";
    } elseif ($start_date >= $end_date) {
        $errors[] = "End date must be after the start date.";
    }

    if (empty($errors)) {
        try {
            $contractController->updateContract($contractId, $start_date, $end_date);
            header('Location: list_contracts.php');
            exit();

        } catch (Exception $e) {
            $errors[] = "Error updating contract: " . $e->getMessage();
        }
    }
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p class='error-message'>" . htmlspecialchars($error) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Contract</title>
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
            max-width: 600px;
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
        .form-group input[type="date"] {
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
            .form-group input[type="date"] {
                flex-basis: 100%;
            }

            input[type="submit"] {
                width: auto;
            }
            .error {
            color: red;
            font-size: 0.875em;
            margin-top: 5px;
        }
        }
    </style>
</head>
<body>
   <div style="padding-top: 100px;">

    <h2>Update Contract</h2>


    <form action="" method="POST">
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
    <input type="submit" value="Update Contract">
</form>


    <div class="return-link">
        <a href="list_contracts.php">Return to List Contracts</a>
    </div>
  </div>
  <?php include('footer.php'); ?>

</body>
</html>
