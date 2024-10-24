
<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $car_id !== null) { // Check if the request method is GET and if car_id is set
    try {
        // Attempt to add a contract
        $carsController->addContract($user_id, $car_id, $start_date, $end_date);
        $message = "Contract added successfully."; // Success message
    } catch (Exception $e) {
        $message = "Error adding contract: " . $e->getMessage(); // Error message
    }
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
                            <a href="view_car.php?id=<?= $car['id']; ?>" class="btn btn-outline-info action-button view">View</a>
                            <?php if ($car['disponible'] === 1 && $currentUser['role']=== 'client'): ?>
                                <a href="#" class="btn btn-outline-success action-button book-now" 
                                data-toggle="modal" data-target="#carModal"
                                data-car-id="<?= $car['id']; ?>"
                                data-car-title="<?= $car['vehicletitle']; ?>"
                                data-price="<?= $car['priceperday']; ?>"
                                data-user-id="<?= $currentUser['id']; ?>"
                                data-matricule="<?= $car['matricule']; ?>"
                                data-start-date="<?= $start_date; ?>"
                                data-end-date="<?= $end_date; ?>">
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
      <!-- Modal for Car Booking -->
<div class="modal fade" id="carModal" tabindex="-1" role="dialog" aria-labelledby="carModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carModalLabel">Car Booking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="totalprice">Total Price:</label>
                        <input type="text" id="totalprice" name="totalprice" readonly>
                    </div>
                    <input type="hidden" name="user_id" id="user_id" value="<?= $currentUser['id']; ?>">
                    <input type="hidden" name="car_id" id="car_id">
                    <input type="hidden" name="price_per_day" id="price_per_day" value="">
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Confirm Booking</button>
                    </div>
                </form>
                <p id="modalMessage"></p>
            </div>
        </div>
    </div>
</div>

<!-- Include necessary JavaScript files -->
<script>
    // Handle passing data to modal
$('#carModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var carId = button.data('car-id');
    var carTitle = button.data('car-title');
    var price = button.data('price');
    var userId = button.data('user-id');
    var startDate = button.data('start-date');
    var endDate = button.data('end-date');
    
    var modal = $(this);
    modal.find('#car_id').val(carId);
    modal.find('#car_title').text(carTitle);
    modal.find('#price_per_day').val(price);
    modal.find('#user_id').val(userId);
    modal.find('#start_date').val(startDate);
    modal.find('#end_date').val(endDate);

    // Recalculate total price whenever start or end date changes
    calculateTotalPrice();
});

// Function to calculate total price based on the date range and daily price
function calculateTotalPrice() {
    var startDate = new Date($('#start_date').val());
    var endDate = new Date($('#end_date').val());
    var pricePerDay = parseFloat($('#price_per_day').val());

    if (startDate && endDate && pricePerDay) {
        // Calculate the number of days between the start and end dates
        var timeDiff = endDate - startDate;
        var days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days

        // Ensure that endDate is after startDate
        if (days >= 0) {
            var totalPrice = days * pricePerDay;
            $('#totalprice').val(totalPrice.toFixed(2) + ' Tnd');
        } else {
            $('#totalprice').val('0 Tnd');
        }
    } else {
        $('#totalprice').val('0 Tnd');
    }
}

// Add event listeners to recalculate the total price when the dates change
$('#start_date, #end_date').on('change', function () {
    calculateTotalPrice();
});

</script>
</body>

</html>
