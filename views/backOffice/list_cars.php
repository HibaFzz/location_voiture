
<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['admin']);

include '../../controllers/CarController.php';

$carsController = new CarController();

// Define the fuel types
$essence = 'essence';  
$diesel = 'diesel';  
$electric = 'electric';   
$all = '';              
$currentUser = AuthController::getCurrentUser();

// Initialize filters
$filters = [
    'brand' => $_GET['brand'] ?? '',
    'disponible' => $_GET['disponible'] ?? '',
    'fueltype' => isset($_GET['fueltype']) ? $_GET['fueltype'] : [], 
    'nbrpersonne' => $_GET['nbrpersonne'] ?? '',
    'vehicletitle' => $_GET['vehicletitle'] ?? '',
    'modelyear' => $_GET['modelyear'] ?? '',
    'matricule' => $_GET['matricule'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc'
];

// Define Pagination
$limit = 6;
$page = $_GET['page'] ?? 1; // Get the current page or set to 1 if not defined
$offset = ($page - 1) * $limit; // Calculate offset for SQL query
// Fetch distinct brands and fuel types
$brands = $carsController->getDistinctBrands();
$fuelTypes = $carsController->getDistinctFuelTypes();

// Use filterCars method to fetch cars based on filters
$cars = $carsController->filterCars($filters,$limit, $offset);
$totalContracts = $carsController->getTotalCarsCount($filters);
// Calculate total pages for pagination
$totalPages = ceil($totalContracts / $limit);

$message = ""; // Initialize message variable

// Get the user ID and car ID from the GET parameters, with default values if not set
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null; 
$car_id = isset($_GET['car_id']) ? $_GET['car_id'] : null;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$currentUser = AuthController::getCurrentUser();

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
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 60%;
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
            margin: 0 5px;
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
       
         .actions {
            margin-top: 10px;
        }
        .actions a {
            text-decoration: none;
            margin: 0 5px;
        }
       

        .price {
            color: #007BFF;
            font-size: 18px;
            font-weight: bold;
        }

    </style>
</head>
<?php include('index.php'); ?>
<div style="padding-top: 100px;">

    <h1 class="text-center text-primary mb-4">List of Cars</h1>
    <div class="container">
        <div class="filter-card">
            <form method="GET" action="">
                    <label for="vehicletitle">Vehicle Title:</label>
                    <input type="text" id="vehicletitle" name="vehicletitle" value="<?= $filters['vehicletitle'] ?? ''; ?>">

                    <label for="modelyear">Model Year:</label>
                    <input type="number" id="modelyear" name="modelyear" value="<?= $filters['modelyear'] ?? ''; ?>" min="1900" max="<?= date('Y'); ?>">

                    <label for="matricule">Matricule:</label>
                    <input type="text" id="matricule" name="matricule" value="<?= $filters['matricule'] ?? ''; ?>">

                    <label for="nbrpersonne">Number of Persons:</label>
                    <input type="number" id="nbrpersonne" name="nbrpersonne" value="<?= $filters['nbrpersonne'] ?? ''; ?>" min="1">

                    <input type="submit" value="Research" class="btn btn-outline-primary">


            </form>
            <form method="GET" action="">
                    <label for="brand">Brand:</label>
                    <select id="brand" name="brand">
                        <option value="">All</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand; ?>" <?= (isset($filters['brand']) && $filters['brand'] === $brand) ? 'selected' : ''; ?>><?= $brand; ?></option>
                        <?php endforeach; ?>
                    </select>
        
                <label for="disponible">Available:</label>
                <select id="disponible" name="disponible">
                    <option value="">All</option>
                    <option value="oui" <?= (isset($filters['disponible']) && $filters['disponible'] === 'oui') ? 'selected' : ''; ?>>Yes</option>
                    <option value="non" <?= (isset($filters['disponible']) && $filters['disponible'] === 'non') ? 'selected' : ''; ?>>No</option>
                </select>

                <label for="fueltype">Fuel Type:</label>
                <select id="fueltype" name="fueltype[]">
                    <option value="">All</option>
                    <option value="<?= $essence; ?>" <?= (isset($filters['fueltype']) && in_array($essence, $filters['fueltype'])) ? 'selected' : ''; ?>>Essence</option>
                    <option value="<?= $diesel; ?>" <?= (isset($filters['fueltype']) && in_array($diesel, $filters['fueltype'])) ? 'selected' : ''; ?>>Diesel</option>
                    <option value="<?= $electric; ?>" <?= (isset($filters['fueltype']) && in_array($electric, $filters['fueltype'])) ? 'selected' : ''; ?>>Electric</option>
                
                </select>
                
                <input type="submit" value="Filter" class="btn btn-outline-primary">


            </form>
            <form method="GET" action="">
                <label for="sort_by">Sort By:</label>
                <select id="sort_by" name="sort_by">
                    <option value="">Select</option>
                    <option value="priceperday" <?= (isset($filters['sort_by']) && $filters['sort_by'] === 'priceperday') ? 'selected' : ''; ?>>Price</option>
                    <option value="modelyear" <?= (isset($filters['sort_by']) && $filters['sort_by'] === 'modelyear') ? 'selected' : ''; ?>>Model Year</option>
                    <option value="vehicletitle" <?= ($filters['sort_by'] === 'vehicletitle') ? 'selected' : ''; ?>>Vehicle Title</option>
                </select>

                <label for="order">Order:</label>
                <select id="order" name="order">
                    <option value="asc" <?= (isset($filters['order']) && $filters['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?= (isset($filters['order']) && $filters['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>

                <input type="submit" value="Trier" class="btn btn-outline-primary">
            </form>
        </div>

        <div class="card-container">
            <?php if (!empty($cars)): ?>
                <?php foreach ($cars as $car): ?>
                    <div class="card">
                        <p><span style="font-weight: bold;color :#007BFF"><?= $car['matricule']; ?></span></p> <!-- Matricule added here -->
                        <img src="<?= $car['image'] ?? 'placeholder-image.jpg'; ?>" alt="Car Image">
                        <h2><?= $car['vehicletitle']; ?></h2>
                        <p><?= $car['brand']; ?></p>
                        <p class="price"><?= $car['priceperday']; ?> Tnd/day</p>
                        <p>
                        <strong>Available:</strong> 
                        <?= ($car['disponible'] == 1) 
                            ? '<span style="color: green;">Yes</span>' 
                            : '<span style="color: red;">No</span>'; ?>
                        </p>

                        <p>Year: <?= $car['modelyear']; ?></p>
                        <div class="actions">
                        <a href="view_car.php?id=<?= $car['id']; ?>" class="btn btn-info action-button view">View</a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No cars found matching the criteria.</p>
            <?php endif; ?>
        </div>
         <!-- Pagination Controls -->
         
    </div>
    <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-4" style="display: flex; justify-content: center;">
        <li class="page-item <?= $page == 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page == $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
     


</body>

</html>
