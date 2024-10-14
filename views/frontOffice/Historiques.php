<?php
// Include necessary controllers
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';

// Check user roles for access
AuthController::checkMultipleRoles(['client', 'agent']);
$currentUser = AuthController::getCurrentUser();

// Fetch user contracts
$contractModel = new ContractController();
$contracts = $contractModel->getUserContracts($currentUser['id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract and Payment History</title>

    <!-- Inline CSS for Beautified Table and Layout -->
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
        <h1>Contract and Payment History for <?php echo $currentUser['username']; ?></h1>

        <table>
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Payment</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Date Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contracts)) : ?>
                    <?php foreach ($contracts as $contract) : ?>
                        <tr>
                            <td><?php echo $contract['vehicletitle']; ?></td>
                            <td><?php echo $contract['start_date']; ?></td>
                            <td><?php echo $contract['end_date']; ?></td>
                            <td><?php echo $contract['total_payment']; ?> $</td>
                            <td><?php echo $contract['status']; ?></td>
                            <td><?php echo $contract['payment_status']; ?></td>
                            <td><?php echo $contract['payment_status'] === 'paid' ? $contract['date_paid'] : 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No contract history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="list_cars.php" class="btn-back">Back to Cars</a>
    </div>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>

</body>
</html>
