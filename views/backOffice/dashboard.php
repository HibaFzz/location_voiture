<?php
require_once '../../controllers/StatisticsController.php';
$model = new StatisticsController();

// Fetch the statistics
$totalPaymentsByMonth = $model->getTotalPaymentsByYearMonth();
$mostRentedCarsByBrand = $model->getMostRentedCarsByBrand();
$mostRentedCarsByFuelType = $model->getMostRentedCarsByFuelType();
$totalCars = $model->getTotalCars();
$totalContracts = $model->getTotalContracts();
$totalUsers = $model->getTotalUsers();
$totalCA = $model->getTotalCA();
$topLoyalClients = $model->getTopLoyalClients();
$contractsByMonth = $model->getContractsByPeriod('month');
$contractsByYear = $model->getContractsByPeriod('year');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            flex-wrap: wrap; /* Allow cards to wrap to the next line */
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 0 0 calc(25% - 30px); /* Four cards per row */
            box-sizing: border-box; /* Include padding in the width */
        }
        .full-width-card {
            flex: 0 0 49%; /* Full width for this card */
        }

        h2 {
            margin-bottom: 20px;
        }
        canvas {
            width: 100% !important; /* Full width for the canvas */
            height: 5 !important; /* Maintain aspect ratio */
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #showContractsByMonth {
            background-color: #4CAF50; /* Green */
            color: white;
        }
        #showContractsByMonth:hover {
            background-color: #45a049;
        }
        #showContractsByYear {
            background-color: #008CBA; /* Blue */
            color: white;
        }
        #showContractsByYear:hover {
            background-color: #007bb5;
        }
    </style>
</head>
<?php include('index.php'); ?>
<body>
<div style="padding-top: 100px;">
    <h1>Car Rental Statistics</h1>

    <div class="container">
        <div class="card">
            <h2>Total Cars</h2>
            <p><?php echo $totalCars; ?></p>
        </div>

        <div class="card">
            <h2>Total Contracts</h2>
            <p><?php echo $totalContracts; ?></p>
        </div>

        <div class="card">
            <h2>Total Users</h2>
            <p><?php echo $totalUsers; ?></p>
        </div>

        <div class="card">
            <h2>Total CA</h2>
            <p><?php echo number_format((float)$totalCA, 3); ?> €</p>
        </div>

        <div class="card full-width-card">
            <h2>Most Rented Cars by Brand</h2>
            <canvas id="mostRentedCarsBrandChart"></canvas>
        </div>

        <div class="card full-width-card">
            <h2>Total Payments by Month</h2>
            <canvas id="totalPaymentsChart"></canvas>
        </div>

        <div class="card full-width-card">
            <h2>Top Loyal Clients</h2>
            <canvas id="topLoyalClientsChart"></canvas>
        </div>

        <div class="card full-width-card">
            <h2>Contracts</h2>
            <div class="button-container">
                <button id="showContractsByMonth">Show Contracts by Month</button>
                <button id="showContractsByYear">Show Contracts by Year</button>
            </div>
            <canvas id="contractsChart"></canvas>
        </div>
        <div class="card full-width-card">
            <h2>Most Rented Cars by Fuel Type</h2>
            <canvas id="mostRentedCarsFuelChart"></canvas>
        </div>
    </div>

    <script>
        // Most Rented Cars by Brand
        const brandLabels = <?php echo json_encode(array_column($mostRentedCarsByBrand, 'brand')); ?>;
        const brandData = <?php echo json_encode(array_column($mostRentedCarsByBrand, 'total_rented')); ?>;

        const ctxBrand = document.getElementById('mostRentedCarsBrandChart').getContext('2d');
        new Chart(ctxBrand, {
            type: 'bar',
            data: {
                labels: brandLabels,
                datasets: [{
                    label: 'Most Rented Cars by Brand',
                    data: brandData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Most Rented Cars by Fuel Type
        const fuelLabels = <?php echo json_encode(array_column($mostRentedCarsByFuelType, 'fueltype')); ?>;
        const fuelData = <?php echo json_encode(array_column($mostRentedCarsByFuelType, 'total_rented')); ?>;

        const ctxFuel = document.getElementById('mostRentedCarsFuelChart').getContext('2d');
        new Chart(ctxFuel, {
            type: 'pie',
            data: {
                labels: fuelLabels,
                datasets: [{
                    label: 'Most Rented Cars by Fuel Type',
                    data: fuelData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Most Rented Cars by Fuel Type'
                    }
                }
            }
        });

        // Total Payments by Month
        const totalPaymentsLabels = <?php echo json_encode(array_column($totalPaymentsByMonth, 'payment_period')); ?>;
        const totalPaymentsData = <?php echo json_encode(array_column($totalPaymentsByMonth, 'total_payment')); ?>;

        const ctxTotalPayments = document.getElementById('totalPaymentsChart').getContext('2d');
        new Chart(ctxTotalPayments, {
            type: 'line',
            data: {
                labels: totalPaymentsLabels,
                datasets: [{
                    label: 'Total Payments Over Time',
                    data: totalPaymentsData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Top Loyal Clients
        const clientLabels = <?php echo json_encode(array_column($topLoyalClients, 'nom')); ?>;
        const clientData = <?php echo json_encode(array_column($topLoyalClients, 'total_rented')); ?>;

        const ctxTopClients = document.getElementById('topLoyalClientsChart').getContext('2d');
        new Chart(ctxTopClients, {
            type: 'bar',
            data: {
                labels: clientLabels,
                datasets: [{
                    label: 'Top Loyal Clients',
                    data: clientData,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    // Contracts data
    const contractsMonthLabels = <?php echo json_encode(array_column($contractsByMonth, 'period')); ?>;
    const contractsMonthData = <?php echo json_encode(array_column($contractsByMonth, 'total_contracts')); ?>;

    const contractsYearLabels = <?php echo json_encode(array_column($contractsByYear, 'period')); ?>;
    const contractsYearData = <?php echo json_encode(array_column($contractsByYear, 'total_contracts')); ?>;

    // Chart initialization
    const ctxContracts = document.getElementById('contractsChart').getContext('2d');
    let contractsChart = new Chart(ctxContracts, {
        type: 'line', // Default to contracts by month
        data: {
            labels: contractsMonthLabels,
            datasets: [{
                label: 'Contracts by Month',
                data: contractsMonthData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Button to show contracts by month
    document.getElementById('showContractsByMonth').addEventListener('click', function() {
        contractsChart.data.labels = contractsMonthLabels;
        contractsChart.data.datasets[0].data = contractsMonthData;
        contractsChart.options.scales.y.beginAtZero = true; // Reset y-axis
        contractsChart.update(); // Refresh the chart
    });

    // Button to show contracts by year
    document.getElementById('showContractsByYear').addEventListener('click', function() {
        contractsChart.data.labels = contractsYearLabels;
        contractsChart.data.datasets[0].data = contractsYearData;
        contractsChart.options.scales.y.beginAtZero = true; // Reset y-axis
        contractsChart.update(); // Refresh the chart
    });

    </script>
</body>
</html>
