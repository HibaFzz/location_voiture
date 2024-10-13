
<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

include '../../controllers/CarController.php';

$carsController = new CarController();

// Define the fuel types
$essence = 'essence'; // Example value
$diesel = 'diesel';   // Example value
$all = '';             // Example value

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $car_id !== null) { // Check if the request method is GET and if car_id is set
    $carController = new CarController();
    $result = $carController->addContract($user_id, $car_id, $start_date, $end_date);
    
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
$currentUser = AuthController::getCurrentUser();


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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .modal-body input[type="date"],
        .modal-body input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #selectedCar {
            margin-top: 20px;
        }

        .modal-footer {
            border-top: 1px solid #ddd;
            padding: 10px;
        }

        .modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-footer .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .modal-footer .btn-secondary:hover {
            background-color: #5a6268;
        }

        .modal-footer .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .modal-footer .btn-primary:hover {
            background-color: #0056b3;
        }

       

        .price {
            color: #007BFF;
            font-size: 18px;
            font-weight: bold;
        }

    </style>
</head>
<?php include('header.php'); ?>
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
                            <a href="view_car.php?id=<?= $car['id']; ?>" class="btn btn-outline-info action-button view">View</a>
                            <?php if ($car['disponible'] === 1 && $currentUser['role']=== 'client'): ?>
                                <a href="#" class="btn btn-outline-success action-button book-now" 
                                data-toggle="modal" 
                                data-target="#bookingModal" 
                                data-user_id="4" 
                                data-car_id="<?= $car['id']; ?>" 
                                data-car_title="<?= $car['vehicletitle']; ?>" 
                                data-price_per_day="<?= $car['priceperday']; ?>">
                                    Book now
                                </a>
                            <?php endif; ?>
                            <hr>
                            <?php if ($currentUser['role'] === 'agent'): ?>
                                <a href="update_car.php?id=<?= $car['id']; ?>" class="btn btn-outline-warning action-button">Update</a> |
                                <a href="delete_car.php?id=<?= $car['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this car?');">Delete</a>
                            <?php endif; ?>
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
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>&<?= http_build_query($filters); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>&<?= http_build_query($filters); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page == $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>&<?= http_build_query($filters); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php if ($currentUser['role'] === 'agent'): ?>
        <a href="add_car.php" style="display: block; text-align: center; background-color: #007BFF; color: white; padding: 10px; border-radius: 5px; text-decoration: none; width: 150px; margin: 20px auto;">Add New Car</a>
    <?php endif?>
    <div>
        <?php include('footer.php'); ?>
    </div>
       <!-- Modal Structure -->
       <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book Car: <span id="car_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contractForm">
                    <input type="hidden" id="user_id" name="user_id">
                    <input type="hidden" id="car_id" name="car_id">
                    
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                    
                    <div id="selectedCar" class="mt-3">
                        <strong>Selected Car:</strong> <span id="car_title"></span> at <span id="price_per_day"></span> TND/day.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="contractForm" class="btn btn-primary">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Populate modal fields with car data when "Book now" is clicked
    $('#bookingModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userId = button.data('user_id');
        var carId = button.data('car_id');
        var carTitle = button.data('car_title');
        var pricePerDay = button.data('price_per_day');

        var modal = $(this);
        modal.find('#user_id').val(userId);
        modal.find('#car_id').val(carId);
        modal.find('#car_title').text(carTitle);
        modal.find('#price_per_day').text(pricePerDay);
    });

    // Handle form submission without server-side processing
    $('#contractForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Capture form data
        var userId = $('#user_id').val();
        var carId = $('#car_id').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var carTitle = $('#car_title').text();

        // Simulate a booking success message
        alert('Booking successful! You have booked the car: ' + carTitle + '. Please check "Contracts" for more details.');

        // Redirect to list_cars.php
        window.location.href = 'list_cars.php';
    });
</script>

</body>

</html>
