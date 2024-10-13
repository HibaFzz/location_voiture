<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['admin']);
include '../../controllers/ContractController.php';

$contractController = new ContractController();

if (isset($_GET['id'])) {
    $contract = $contractController->getContractById($_GET['id']); // Fetch contract details by ID
} else {
    echo "No contract ID specified.";
    exit();
}

if (!$contract) {
    echo "Contract not found.";
    exit();
}

// Fetch related car and user details
$car = $contractController->getCarById($contract['car_id']);
$user = $contractController->getUserById($contract['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('index.php'); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Details</title>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container1 {
            max-width: 700px; /* Further reduced width */
            margin: 20px auto;
            padding: 10px; /* Reduced padding */
            background-color: #fff;
            border-radius: 8px; /* Reduced border radius */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.2em; /* Further reduced font size */
            color: #002e5d;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 1.3em; /* Reduced font size */
            color: #555;
        }

        .card {
            background-color: #f9fafc;
            border-radius: 8px; /* Reduced border radius */
            padding: 15px; /* Further reduced padding */
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px; /* Reduced margin between cards */
        }

        .card h3 {
            font-size: 1.3em; /* Reduced font size */
            color: #007bff;
            margin-bottom: 8px; /* Reduced margin */
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }

        .detail-item {
            margin-bottom: 8px; /* Reduced margin between detail items */
        }

        .detail-item span {
            font-weight: bold;
            color: #444;
        }

        .detail-item p {
            margin: 2px 0; /* Further reduced margin for paragraph */
            color: #666;
        }

        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 7px 15px; /* Further reduced padding */
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em; /* Further reduced font size */
            margin-top: 15px; /* Reduced margin */
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div style="padding-top: 100px;">
    <div class="container1">
        <div class="header">
        <h1 class="text-center text-primary mb-4">Contract Informations</h1>
            <h2>Contract #<?= $contract['id']; ?></h2>
        </div>

        <!-- User Information Section -->
        <div class="card">
            <h3>User Information</h3>
            <div class="detail-item">
                <span>Name:</span>
                <p><?= $user['nom'] . ' ' . $user['prenom']; ?></p>
            </div>
            <div class="detail-item">
                <span>Email:</span>
                <p><?= $user['email']; ?></p>
            </div>
            <div class="detail-item">
                <span>Telephone:</span>
                <p><?= $user['numtelephone']; ?></p>
            </div>
            <div class="detail-item">
                <span>CIN:</span>
                <p><?= $user['cin']; ?></p>
            </div>
        </div>

        <!-- Car Information Section -->
        <div class="card">
            <h3>Car Information</h3>
            <div class="detail-item">
                <span>Car:</span>
                <p><?= $car['vehicletitle']; ?></p>
            </div>
            <div class="detail-item">
                <span>Matricule:</span>
                <p><?= $car['matricule']; ?></p>
            </div>
            <div class="detail-item">
                <span>Brand:</span>
                <p><?= $car['brand']; ?></p>
            </div>
            <div class="detail-item">
                <span>Fuel Type:</span>
                <p><?= $car['fueltype']; ?></p>
            </div>
        </div>

        <!-- Contract Information Section -->
        <div class="card">
            <h3>Contract Information</h3>
            <div class="detail-item">
                <span>Start Date:</span>
                <p><?= $contract['start_date']; ?></p>
            </div>
            <div class="detail-item">
                <span>End Date:</span>
                <p><?= $contract['end_date']; ?></p>
            </div>
            <div class="detail-item">
                <span>Total Price:</span>
                <p><?= number_format($car['priceperday'], 2); ?> TND</p>
            </div>
        </div>

        <div style="text-align: center;"> <!-- Center the back button -->
            <a href="list_contracts.php" class="back-button">Back to Contracts List</a>
        </div>
    </div>
</body>
</html>
