<?php 
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';

// Check user roles for access
AuthController::checkMultipleRoles(['client', 'agent']);
$currentUser = AuthController::getCurrentUser();

// Get current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Fetch recently returned cars for the current page
$contractController = new ContractController();
$result = $contractController->getRecentlyReturnedCarsLast7Days($page);
$recentlyReturnedCars = $result['cars'];
$totalPages = $result['totalPages'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recently Returned Cars - Last 7 Days</title>

    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Body Style */
        body {
            background-color: #eef2f7;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Main Content Styling */
        .container1 {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            max-width: 1100px;
            width: 100%;
            margin: 40px auto;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Header */
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 30px;
            font-weight: bold;
        }

        /* Beautified Table Style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            border-radius: 15px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px 20px;
            text-align: center;
            font-size: 16px;
            border-bottom: 2px solid #ddd;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        th {
            background-color: #007bff;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        td {
            color: #555;
            font-size: 15px;
        }

        /* Row Hover Effect */
        tr:hover {
            background-color: rgba(0, 123, 255, 0.3);
            transform: scale(1.02);
        }

        /* Stripe Effect */
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Rounded Corners */
        tbody tr {
            transition: transform 0.2s;
        }

        tbody tr:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Button Style */
        .btn-back {
            display: inline-block;
            padding: 14px 28px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s;
            font-size: 16px;
            margin-top: 30px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.5);
        }

        .btn-back:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Footer Style */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            width: 100%;
            position: relative;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container1 {
                padding: 20px;
            }

            th,
            td {
                padding: 10px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <!-- Include Header -->
    <?php include('header.php'); ?>

    <!-- Main Content Container -->
    <div class="container1">
        <h1>Recently Returned Cars (Last 7 Days)</h1>

        <table>
            <thead>
                <tr>
                    <th>Car ID</th>
                    <th>Matricule</th>
                    <th>Vehicle Title</th>
                    <th>Status</th>
                    <th>Return Date</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentlyReturnedCars)) : ?>
                    <?php foreach ($recentlyReturnedCars as $car) : ?>
                        <tr>
                            <td><?php echo $car['car_id']; ?></td>
                            <td><?php echo $car['matricule']; ?></td>
                            <td><?php echo $car['vehicletitle']; ?></td>
                            <td><?php echo $car['status']; ?></td>
                            <td>
                                <?php echo $car['status'] == 'completed' ? $car['date_updated'] : $car['date_canceled']; ?>
                            </td>
                            <td><?php echo $car['username']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">No cars have been recently returned in the last 7 days.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
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

        <a href="list_cars.php" class="btn-back">Back to Cars</a>
    </div>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>

</body>
</html>
