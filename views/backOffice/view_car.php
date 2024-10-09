<?php
include '../../controllers/CarController.php';

$carsController = new CarController();

if (isset($_GET['id'])) {
    $car = $carsController->getCar($_GET['id']);
} else {
    echo "No car ID specified.";
    exit();
}

if (!$car) {
    echo "Car not found.";
    exit();
}

$message = ""; // Initialize message variable

// Get the user ID and car ID from GET parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$car_id = isset($_GET['id']) ? $_GET['id'] : null; // Use 'id' for car ID
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $car_id !== null) { // Check if the request method is GET and if car_id is set
    $result = $carsController->addContract($user_id, $car_id, $start_date, $end_date);
    $message = $result; // Store the result message to display later
}

$car_title = isset($_GET['car_title']) ? $_GET['car_title'] : 'Selected Car'; // Get car title from URL
$price_per_day = isset($_GET['price_per_day']) ? $_GET['price_per_day'] : 0; // Get price per day from URL

// Check for undefined variables
if ($user_id === null) {
    $message = "User ID is not set.";
}

if ($car_id === null) {
    $message = "Car ID is not set.";
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('index.php'); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <h1 class="text-center text-primary mb-4">Luxury Car Rental - Car Details</h1>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container1 {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 3.5em;
            color: #002e5d;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 1.8em;
            color: #555;
        }

        .content {
            display: flex;
            gap: 40px;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card {
            flex: 1;
            background-color: #f9fafc;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .car-image {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px; /* Space between image and overview */
            display: flex; /* Use flexbox for centering */
            justify-content: center; /* Center the content horizontally */
        }

        .car-image img {
            width: 50%;
            height: auto;
            object-fit: cover;
        }

        .car-info h2, .vehicle-overview h2 {
            font-size: 2em;
            color: #0056b3;
            margin-bottom: 10px;
        }

        .car-detail-item {
            margin-bottom: 15px;
        }

        .car-detail-item span {
            font-weight: bold;
            color: #444;
        }

        .car-detail-item p {
            margin: 5px 0;
            color: #666;
        }

        .price-section {
            margin-top: auto; /* Push price section to the bottom */
            text-align: center;
        }

        .price-per-day {
            font-size: 2.8em;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }

        .action-button {
            background-color: #007bff;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.2em;
            display: inline-block;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin: 0 10px;
        }

        .action-button:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.1em;
            margin-top: 25px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        .vehicle-overview {
            margin-top: 20px; /* Space above the vehicle overview */
            padding: 10px;
            border-radius: 10px;
            text-align: center; /* Centering the content */
        }

        .vehicle-overview p {
            color: #444;
            font-size: 1em;
            line-height: 1.6;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }

            .card {
                width: 100%;
            }
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            overflow-y: auto;
        }

        .modal-dialog {
            margin: 5% auto;
            max-width: 500px;
            width: 90%;
        }

        .modal-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }

        .modal-header {
            border-bottom: 1px solid #ddd;
        }

        .modal-title {
            color: #007BFF;
            font-size: 24px;
        }

        .close {
            font-size: 28px;
            cursor: pointer;
            background: none;
            border: none;
            color: #aaa;
        }

        .close:hover {
            color: #333;
        }

        /* Modal Body Styling */
        .modal-body {
            padding: 20px;
        }

        .modal-body label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .modal-body input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        /* Modal Footer */
        .modal-footer {
            border-top: 1px solid #ddd;
            padding: 10px 15px;
        }

        .modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <div class="container1">
        <div class="header">
            <h1>Luxury Car Rental</h1>
            <h2><?= $car['vehicletitle']; ?></h2>
        </div>

        <div class="content">
            <div class="card" style="flex: 1.3;"> <!-- Merged card for image and overview -->
                <div class="car-image">
                    <?php if (!empty($car['image'])): ?>
                        <img src="<?= $car['image']; ?>" alt="Car Image">
                    <?php else: ?>
                        <p>No image available</p>
                    <?php endif; ?>
                </div>

                <div class="vehicle-overview">
                    <h2>Vehicle Overview</h2>
                    <p><?= $car['vehicleoverview']; ?></p>
                </div>
                
                <h2>Car Details</h2>
                <div class="car-detail-item">
                    <span>Matricule:</span>
                    <p><?= $car['matricule']; ?></p>
                </div>
                <div class="car-detail-item">
                    <span>Brand:</span>
                    <p><?= $car['brand']; ?></p>
                </div>
                <div class="car-detail-item">
                    <span>Fuel Type:</span>
                    <p><?= $car['fueltype']; ?></p>
                </div>
                <div class="car-detail-item">
                    <span>Model Year:</span>
                    <p><?= $car['modelyear']; ?></p>
                </div>
                <div class="car-detail-item">
                    <span>Seats:</span>
                    <p><?= $car['nbrpersonne']; ?></p>
                </div>
                <div class="car-detail-item">
                    <span>Availability:</span>
                    <?= ($car['disponible'] == 1) 
                            ? '<span style="color: green;">Yes</span>' 
                            : '<span style="color: red;">No</span>'; ?>
                </div>

                <div class="price-section">
                    <h2>Starting from</h2>
                    <div class="price-per-day">€<?= number_format($car['priceperday'], 2); ?> / day</div>
                </div>

            </div>
        </div>

        <div style="text-align: center;"> <!-- Center the back button -->
            <a href="list_cars.php" class="back-button">Back to Car List</a>
        </div>
    </div>




</body>
</html>



