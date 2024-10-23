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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            position: relative; /* Ensure footer positioning */
            min-height: 100vh; /* To make sure the body is at least full height */
        }

        .container1 {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding-bottom: 80px; /* Extra padding to avoid footer overlap */
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-toggle {
            display: block;
            width: 200px;
            margin: 10px auto;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-toggle:hover {
            background-color: #0056b3;
        }

        .table-container1 {
            display: none;
        }

        .table-container1.active {
            display: block;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative; /* Changed from fixed */
            bottom: 0;
            width: 100%;
            clear: both; /* Make sure footer clears the content above */
        }
    </style>

    <script>
        // Function to toggle between contract and payment history tables
        function toggleTables() {
            const contractHistory = document.getElementById('contract-history');
            const paymentHistory = document.getElementById('payment-history');
            if (contractHistory.classList.contains('active')) {
                contractHistory.classList.remove('active');
                paymentHistory.classList.add('active');
            } else {
                contractHistory.classList.add('active');
                paymentHistory.classList.remove('active');
            }
        }
    </script>
</head>

<body>

    <!-- Include Header -->
    <?php include('header.php'); ?>

    <div class="container1">
        <div style="padding-top: 100px;">
            <h1>Contract and Payment History</h1>

            <!-- Toggle Button -->
            <button class="btn-toggle" onclick="toggleTables()">Payment History</button>

            <!-- Contract Action History Table -->
            <div id="contract-history" class="table-container1 active">
                <h2>Contract Action History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Action</th>
                            <th>Action Date</th>
                            <th>Total Payment</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)) : ?>
                            <?php foreach ($contracts as $contract) : ?>
                                <!-- Display Date Added -->
                                <tr>
                                    <td><?php echo $contract['vehicletitle']; ?></td>
                                    <td>Contract Added</td>
                                    <td><?php echo $contract['date_added']; ?></td>
                                    <td><?php echo $contract['total_payment']; ?> $</td>
                                    <td><?php echo $contract['status']; ?></td>
                                    <td><?php echo $contract['payment_status']; ?></td>
                                </tr>
                                
                                <!-- Display Date Updated (if applicable) -->
                                <?php if (!empty($contract['date_updated'])) : ?>
                                <tr>
                                    <td><?php echo $contract['vehicletitle']; ?></td>
                                    <td>Contract Updated</td>
                                    <td><?php echo $contract['date_updated']; ?></td>
                                    <td><?php echo $contract['total_payment']; ?> $</td>
                                    <td><?php echo $contract['status']; ?></td>
                                    <td><?php echo $contract['payment_status']; ?></td>
                                </tr>
                                <?php endif; ?>

                                <!-- Display Date Canceled (if applicable) -->
                                <?php if (!empty($contract['date_canceled'])) : ?>
                                <tr>
                                    <td><?php echo $contract['vehicletitle']; ?></td>
                                    <td>Contract Canceled</td>
                                    <td><?php echo $contract['date_canceled']; ?></td>
                                    <td>N/A</td>
                                    <td><?php echo $contract['status']; ?></td>
                                    <td><?php echo $contract['payment_status']; ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">No contract history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payment History Table -->
            <div id="payment-history" class="table-container1">
                <h2>Payment History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Total Payment</th>
                            <th>Payment Date</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)) : ?>
                            <?php foreach ($contracts as $contract) : ?>
                                <?php if (!empty($contract['date_paid'])) : ?>
                                <tr>
                                    <td><?php echo $contract['vehicletitle']; ?></td>
                                    <td><?php echo $contract['total_payment']; ?> $</td>
                                    <td><?php echo $contract['date_paid']; ?></td>
                                    <td><?php echo $contract['payment_status']; ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4">No payment history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>

</body>

</html>
