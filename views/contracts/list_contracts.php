<?php
include '../../controllers/ContractController.php';

$contractController = new ContractController();
$filters = [
    'status' => $_GET['status'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'search' => $_GET['search'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc'
];

// Fetch contracts based on filters
$contracts = $contractController->filterContracts($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Contracts</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 30px;
        }
        .container {
            display: flex;
            gap: 20px; /* Space between filter and contract list */
        }
        .filter-card {
            flex: 1; /* Allow the filter card to take equal space */
            max-width: 300px; /* Set a max width for the filter card */
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
        .filter-card select, .filter-card input[type="date"], .filter-card input[type="text"], .filter-card input[type="submit"] {
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
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Responsive grid */
            gap: 20px; /* Space between the contract cards */
            flex: 3; /* Allow the contract list to take more space */
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px); /* Lift effect on hover */
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
            color: #007BFF;
            margin: 0 5px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column; /* Stack the filter card above the contract list on small screens */
            }
            .filter-card {
                max-width: none; /* Allow full width on small screens */
            }
        }
    </style>
</head>
<body>
    <h1>List of Contracts</h1>

    <div class="container">
        <div class="filter-card">
            <form method="GET" action="">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="">All</option>
                    <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="completed" <?= ($filters['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="canceled" <?= ($filters['status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                </select>

                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= $filters['start_date']; ?>">

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?= $filters['end_date']; ?>">

                <label for="search">Search:</label>
                <input type="text" id="search" name="search" value="<?= $filters['search']; ?>" placeholder="Search by ID, User ID, etc.">

                <label for="sort_by">Sort by:</label>
                <select id="sort_by" name="sort_by">
                    <option value="">Select...</option>
                    <option value="start_date" <?= ($filters['sort_by'] === 'start_date') ? 'selected' : ''; ?>>Start Date</option>
                    <option value="end_date" <?= ($filters['sort_by'] === 'end_date') ? 'selected' : ''; ?>>End Date</option>
                    <option value="total_payment" <?= ($filters['sort_by'] === 'total_payment') ? 'selected' : ''; ?>>Total Payment</option>
                </select>

                <label for="order">Order:</label>
                <select id="order" name="order">
                    <option value="asc" <?= ($filters['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?= ($filters['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>

                <input type="submit" value="Filter">
            </form>
        </div>

        <div class="card-container">
            <?php foreach ($contracts as $contract): ?>
                <div class="card">
                    <h2>Contract ID: <?= $contract['id']; ?></h2>
                    <p><strong>User ID:</strong> <?= $contract['user_id']; ?></p>
                    <p><strong>Car ID:</strong> <?= $contract['car_id']; ?></p>
                    <p><strong>Start Date:</strong> <?= $contract['start_date']; ?></p>
                    <p><strong>End Date:</strong> <?= $contract['end_date']; ?></p>
                    <p><strong>Total Payment:</strong> <?= $contract['total_payment']; ?> MAD</p>
                    <p><strong>Status:</strong> <?= ucfirst($contract['status']); ?></p>
                    <div class="actions">
                        <a href="view_contract.php?id=<?= $contract['id']; ?>">View</a> |
                        <a href="update_contract.php?id=<?= $contract['id']; ?>">Edit</a> |
                        <a href="delete_contract.php?id=<?= $contract['id']; ?>" onclick="return confirm('Are you sure you want to delete this contract?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <br>
    <a href="add_contract.php" style="display: block; text-align: center; background-color: #007BFF; color: white; padding: 10px; border-radius: 5px; text-decoration: none; width: 150px; margin: 20px auto;">Add New Contract</a>
</body>
</html>
