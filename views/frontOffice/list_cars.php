<?php
include '../../controllers/CarController.php';

$carsController = new CarController();

// Define the fuel types
$essence = 'essence'; // Example value
$diesel = 'diesel';   // Example value
$essence = 'essence'; // Example value
$all = '';   // Example value

$filters = [
    'brand' => $_GET['brand'] ?? '',
    'disponible' => $_GET['disponible'] ?? '',
    // Using an array to hold multiple fuel types
    'fueltype' => isset($_GET['fueltype']) ? $_GET['fueltype'] : [], 
    'nbrpersonne' => $_GET['nbrpersonne'] ?? '',
    'vehicletitle' => $_GET['vehicletitle'] ?? '',
    'modelyear' => $_GET['modelyear'] ?? '',
    'matricule' => $_GET['matricule'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc'
];

// Fetch distinct brands and fuel types
$brands = $carsController->getDistinctBrands();
$fuelTypes = $carsController->getDistinctFuelTypes();

// Use filterCars method to fetch cars based on filters
$cars = $carsController->filterCars($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Cars</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 30px;
        }
        .container {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .filter-card {
            flex: 1;
            max-width: 300px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .filter-card form {
            display: flex;
            flex-direction: column;
        }
        .filter-card label {
            margin-top: 10px;
        }
        .filter-card select, .filter-card input[type="number"], .filter-card input[type="text"], .filter-card input[type="submit"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-card input[type="submit"] {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-card input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            flex: 3;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .card h2 {
            font-size: 20px;
            margin: 10px 0;
            color: #007BFF;
        }
        .card p {
            color: #555;
            margin: 5px 0;
        }
        .actions {
            margin-top: 10px;
        }
        .actions a {
            text-decoration: none;
            color: #007BFF;
            margin: 0 5px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .filter-card {
                max-width: none;
            }
        }
        footer {
            position: relative;
            bottom: 0;
            width: 100%;
            padding: 20px;
            text-align: center;
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<?php include('header.php'); ?>
<body>

    <h1>List of Cars</h1>
    <div class="container">
        <div class="filter-card">
            <form method="GET" action="">
                <label for="vehicletitle">Vehicle Title:</label>
                <input type="text" id="vehicletitle" name="vehicletitle" value="<?= $filters['vehicletitle']; ?>">

                <label for="modelyear">Model Year:</label>
                <input type="number" id="modelyear" name="modelyear" value="<?= $filters['modelyear']; ?>" min="1900" max="<?= date('Y'); ?>">

                <label for="matricule">Matricule:</label>
                <input type="text" id="matricule" name="matricule" value="<?= $filters['matricule']; ?>">

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
                <select id="fueltype" name="fueltype[]">
                    <option value="">All</option>
                    <option value="<?= $essence; ?>" <?= in_array($essence, (array)$filters['fueltype']) ? 'selected' : ''; ?>>Essence</option>
                    <option value="<?= $diesel; ?>" <?= in_array($diesel, (array)$filters['fueltype']) ? 'selected' : ''; ?>>Diesel</option>
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
                    <img src="<?= $car['image'] ?? 'placeholder-image.jpg'; ?>" alt="Car Image">
                    <h2><?= $car['vehicletitle']; ?></h2>
                    <p><?= $car['brand']; ?></p>
                    <p class="price">$<?= $car['priceperday']; ?>/day</p>
                    <p>
                        <strong>Available:</strong> 
                        <?= ($car['disponible'] === 'true' || $car['disponible'] === 1) 
                            ? '<span style="color: green;">Oui</span>' 
                            : '<span style="color: red;">Non</span>'; ?>
                    </p>
                    <p>Year: <?= $car['modelyear']; ?></p>
                    <div class="actions">
                        <a href="book_car.php?user_id=4&car_id=<?= $car['id']; ?>" class="action-button book-now">Book now</a>
                        <a href="view_car.php?id=<?= $car['id']; ?>" class="action-button view">View</a> 
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>Car Rental System - All rights reserved &copy; <?= date('Y'); ?></p>
    </footer>

</body>
</html>
