<?php
include '../../controllers/CarController.php';

$message = ""; // Initialize message variable

// Get the user ID and car ID from GET parameters
$user_id = $_GET['user_id'] ?? null;
$car_id = $_GET['car_id'] ?? null;
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($car_id === null) {
        $message = "Car ID is not set.";
    } elseif ($user_id === null) {
        $message = "User ID is not set.";
    } else {
        $carController = new CarController();
        $message = $carController->addContract($user_id, $car_id, $start_date, $end_date);
    }
}

// Retrieve car title and price per day, if available
$car_title = $_GET['car_title'] ?? 'Selected Car';
$price_per_day = $_GET['price_per_day'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Booking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Book a Car</h1>
        <div id="bookingForm">
            <h2>Booking Details</h2>
            <form id="contractForm" action="" method="GET">
                <input type="hidden" id="user_id" name="user_id" value="4"<?php echo $user_id; ?>">
                <input type="hidden" id="car_id" name="car_id" value="<?php echo $car_id; ?>">
                
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required value="<?php echo htmlspecialchars($start_date); ?>">
                
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required value="<?php echo htmlspecialchars($end_date); ?>">

                <button type="submit">Book Car</button>
            </form>
            <div id="message"><?php echo $message; ?></div>
            <p id="selectedCar">
                <strong>Selected Car:</strong> <?php echo htmlspecialchars($car_title); ?> at $<?php echo htmlspecialchars($price_per_day); ?> per day.
            </p>
        </div>
    </div>
</body>
</html>
