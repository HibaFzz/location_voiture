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
?>

<!DOCTYPE html>
<html lang="en">
<?php include('header.php'); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Car Rental - Car Details</title>
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #eef2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 3.2em;
            color: #0056b3;
        }

        .header h2 {
            font-size: 2.5em;
            color: #333;
            margin: 10px 0;
        }

        .content {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 30px;
            justify-content: center;
        }

        .car-image {
            flex: 1;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .car-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .car-info {
            flex: 1.5;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .car-info h2 {
            font-size: 2em;
            color: #0056b3;
            margin-bottom: 20px;
        }

        .car-detail-item {
            margin-bottom: 15px;
        }

        .car-detail-item span {
            font-weight: bold;
            color: #555;
        }

        .car-detail-item p {
            margin: 5px 0;
            color: #777;
        }

        .price-section {
            margin-top: 20px;
            text-align: center;
        }

        .price-per-day {
            font-size: 2.5em;
            font-weight: bold;
            color: #28a745;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 15px 35px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            font-size: 1.2em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-block;
            background-color: #6c757d;
            color: #ffffff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            font-size: 1em;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            .car-image,
            .car-info {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Luxury Car Rental</h1>
            <h2>Experience the Ride of Your Life</h2>
            <h2><?= $car['vehicletitle']; ?></h2>
        </div>

        <div class="content">
            <div class="car-image">
                <?php if (!empty($car['image'])): ?>
                    <img src="<?= $car['image']; ?>" alt="Car Image">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>

            <div class="car-info">
                <h2>Car Details</h2>
                <div class="car-detail-item">
                    <span>ID:</span>
                    <p><?= $car['id']; ?></p>
                </div>
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

                <div class="price-section">
                    <h2>Starting from</h2>
                    <div class="price-per-day">€<?= number_format($car['priceperday'], 2); ?> / day</div>
                    <a href="booking.php?id=<?= $car['id']; ?>" class="button">Book Now</a>
                </div>
            </div>
        </div>

        <a href="list_cars.php" class="back-button">Back to Car List</a>
    </div>
</body>
<?php include('footer.php'); ?>
</html>
