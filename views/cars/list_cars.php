<?php
include '../../controllers/CarController.php';

$carsController = new CarController();
$filters = [
    'brand' => $_GET['brand'] ?? '',
    'disponible' => $_GET['disponible'] ?? '',
    'fueltype' => $_GET['fueltype'] ?? '',
    'nbrpersonne' => $_GET['nbrpersonne'] ?? '' ,// Updated variable name here
    'vehicletitle' => $_GET['vehicletitle'] ?? '',  // Added vehicletitle
    'modelyear' => $_GET['modelyear'] ?? '',        // Added modelyear
    'matricule' => $_GET['matricule'] ?? '', 
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc'
];

// Fetch distinct brands and fuel types
$brands = $carsController->getDistinctBrands();
$fuelTypes = $carsController->getDistinctFuelTypes();

// Use filterCars method
$cars = $carsController->filterCars($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Cars</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <h1>List of Cars</h1>

    <div class="container">

        
            
        
        
        <div class="filter-card">


            <form method="GET" action="">
                <!-- Existing fields -->

                <label for="vehicletitle">Vehicle Title:</label>
                <input type="text" id="vehicletitle" name="vehicletitle" value="<?= $filters['vehicletitle']; ?>">

                <label for="modelyear">Model Year:</label>
                <input type="number" id="modelyear" name="modelyear" value="<?= $filters['modelyear']; ?>" min="1900" max="<?= date('Y'); ?>">

                <label for="matricule">Matricule:</label>
                <input type="text" id="matricule" name="matricule" value="<?= $filters['matricule']; ?>">

                <!-- Other filters and submit button -->
                <input type="submit" value="Filter">
            </form>

        
            
            <form method="GET" action="">
                <label for="brand">Brand:</label>
                <select id="brand" name="brand">
                    <option value="">All</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand; ?>" <?= ($filters['brand'] === $brand) ? 'selected' : ''; ?>><?= $brand; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="disponible">Available:</label>
                <select id="disponible" name="disponible">
                    <option value="">All</option>
                    <option value="oui" <?= ($filters['disponible'] === 'oui') ? 'selected' : ''; ?>>Yes</option>
                    <option value="non" <?= ($filters['disponible'] === 'non') ? 'selected' : ''; ?>>No</option>
                </select>

                <label for="fueltype">Fuel Type:</label>
                <select id="fueltype" name="fueltype">
                    <option value="">All</option>
                    <?php foreach ($fuelTypes as $fuelType): ?>
                        <option value="<?= $fuelType; ?>" <?= ($filters['fueltype'] === $fuelType) ? 'selected' : ''; ?>><?= $fuelType; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="nbrpersonne">Number of Persons:</label>
                <input type="number" id="nbrpersonne" name="nbrpersonne" value="<?= $filters['nbrpersonne']; ?>" min="1">

                <label for="sort_by">Sort by:</label>
                <select id="sort_by" name="sort_by">
                    <option value="">Select...</option>
                    <option value="vehicletitle" <?= ($filters['sort_by'] === 'vehicletitle') ? 'selected' : ''; ?>>Vehicle Title</option>
                    <option value="priceperday" <?= ($filters['sort_by'] === 'priceperday') ? 'selected' : ''; ?>>Price</option>
                    <option value="nbrpersonne" <?= ($filters['sort_by'] === 'nbrpersonne') ? 'selected' : ''; ?>>Number of Persons</option>
                </select>

                <label for="order">Order:</label>
                <select id="order" name="order">
                    <option value="asc" <?= ($filters['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?= ($filters['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>

                <input type="submit" value="Filter">
            </form>
        </div>

        <div class="card-container">
            <?php foreach ($cars as $car): ?>
                <div class="card">
                    <h2><?= $car['matricule']; ?></h2>
                    <div>
                        <?php if (!empty($car['image'])): ?>
                            <img src="<?= $car['image']; ?>" alt="Car Image">
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
                    </div>
                    <p><strong>Vehicle Title:</strong> <?= $car['vehicletitle']; ?></p>
                    <p><strong>Brand:</strong> <?= $car['brand']; ?></p>
                    <p><strong>Price Per Day:</strong> <?= $car['priceperday']; ?> MAD</p>
                    <p><strong>Available:</strong> <?= ($car['disponible'] === 'oui') ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>'; ?></p>
                    <p><strong>Fuel Type:</strong> <?= $car['fueltype']; ?></p>
                    <p><strong>Number of Persons:</strong> <?= $car['nbrpersonne']; ?></p> <!-- Updated variable name here -->
                    <div class="actions">
                        <a href="view_car.php?id=<?= $car['id']; ?>">View</a> |
                        <a href="update_car.php?id=<?= $car['id']; ?>">Edit</a> |
                        <a href="delete_car.php?id=<?= $car['id']; ?>" onclick="return confirm('Are you sure you want to delete this car?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <br>
    <a href="add_car.php" style="display: block; text-align: center; background-color: #007BFF; color: white; padding: 10px; border-radius: 5px; text-decoration: none; width: 150px; margin: 20px auto;">Add New Car</a>
</body>
</html>
