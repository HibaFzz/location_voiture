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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details</title>
</head>
<body>
    <h1>Car Details</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <td><?= $car['id']; ?></td>
        </tr>
        <tr>
            <th>Matricule</th>
            <td><?= $car['matricule']; ?></td>
        </tr>
        <tr>
            <th>Image</th>
            <td>
                <?php if (!empty($car['image'])): ?>
                    <img src="<?= $car['image']; ?>" alt="Car Image" style="width:100px; height:auto;">
                <?php else: ?>
                    No image available
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Vehicle Title</th>
            <td><?= $car['vehicletitle']; ?></td>
        </tr>
        <tr>
            <th>Brand</th>
            <td><?= $car['brand']; ?></td>
        </tr>
        <tr>
            <th>Vehicle Overview</th>
            <td><?= $car['vehicleoverview']; ?></td>
        </tr>
        <tr>
            <th>Price Per Day</th>
            <td><?= $car['priceperday']; ?></td>
        </tr>
        <tr>
            <th>Fuel Type</th>
            <td><?= $car['fueltype']; ?></td>
        </tr>
        <tr>
            <th>Model Year</th>
            <td><?= $car['modelyear']; ?></td>
        </tr>
        <tr>
            <th>Number of Persons</th>
            <td><?= $car['nbrpersonne']; ?></td>
        </tr>
    </table>
    <br>
    <a href="list_cars.php">Back to Car List</a>
</body>
</html>
