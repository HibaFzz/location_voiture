<?php
include '../../controllers/CarController.php';

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Booking</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Book a Car</h1>
        <div id="bookingForm">
            <h2>Booking Details</h2>
            <form id="contractForm" action="" method="GET"> <!-- Submit to the same file -->
                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>"> <!-- Removed htmlspecialchars -->
                <input type="hidden" id="car_id" name="car_id" value="<?php echo $car_id; ?>">
                
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>

                <button type="submit">Book Car</button>
            </form>
            <div id="message"><?php echo $message; ?></div> <!-- Display success or error messages -->
            <p id="selectedCar">
                <strong>Selected Car:</strong> <?php echo $car_title; ?> at $<?php echo $price_per_day; ?> per day.
            </p>
        </div>
    </div>
</body>
</html>