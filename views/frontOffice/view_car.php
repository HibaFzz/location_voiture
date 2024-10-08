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
        .modal-dialog {
            max-width: 600px;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            font-size: 1.5em;
            color: #0056b3;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 10px 20px;
        }

        .modal-footer .btn {
            padding: 10px 20px;
            border-radius: 6px;
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

                <?php if ($car['disponible'] === 1): ?>
                    <div style="text-align: center;"> <!-- Center the buttons -->
                        <a href="#" class="action-button book-now" 
                        data-toggle="modal" 
                        data-target="#bookingModal" 
                        data-user_id="4" 
                        data-car_id="<?= $car['id']; ?>" 
                        data-car_title="<?= $car['vehicletitle']; ?>" 
                        data-price_per_day="<?= $car['priceperday']; ?>">
                            Book now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="text-align: center;"> <!-- Center the back button -->
            <a href="list_cars.php" class="back-button">Back to Car List</a>
        </div>
    </div>

    <!-- Modal -->
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
    <?php include('footer.php'); ?>
</body>
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
</html>






