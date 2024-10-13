<?php
include '../../controllers/ContractController.php'; // Include the ContractController
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

$contractController = new ContractController(); // Instance of ContractController
$errors = []; // Initialize an array to hold error messages

// Check if contract_id is provided via URL
if (isset($_GET['id'])) {
    $contractId = (int)$_GET['id']; // Cast to integer for safety

    // Fetch the contract details using the provided contract ID
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($contract)) {
    // Get input values
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    // Validate start and end dates
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

    // If no errors, proceed to update the contract
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

// Display errors (if any)
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
        }
    </style>
</head>
<body>
   <div style="padding-top: 100px;"> <!-- Added padding-top to increase space from header -->

    <h2>Update Contract</h2>

    <!-- Form to update contract -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo isset($contract['start_date']) ? htmlspecialchars($contract['start_date']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo isset($contract['end_date']) ? htmlspecialchars($contract['end_date']) : ''; ?>" required>
        </div>

        <input type="submit" value="Update Contract">
    </form>

    <!-- Return to list of contracts link -->
    <div class="return-link">
        <a href="list_contracts.php">Return to List Contracts</a>
    </div>
  </div>
  <?php include('footer.php'); ?>

</body>
</html>
