<?php
include '../../controllers/ContractController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

// Now you can create an instance of ContractController
$contractController = new ContractController();
$filters = [
    'status' => $_GET['status'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'search' => $_GET['search'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc',
    'user_name' => $_GET['user_name'] ?? '', 
    'vehicletitle' => $_GET['vehicletitle'] ?? ''
];

// Pagination settings
$limit = 9;
$page = $_GET['page'] ?? 1; // Get the current page or set to 1 if not defined
$offset = ($page - 1) * $limit; // Calculate offset for SQL query

// Fetch contracts based on filters and pagination
$contracts = $contractController->filterContracts($filters, $limit, $offset);

// Get total number of contracts for pagination
$totalContracts = $contractController->getTotalContracts($filters);

// Calculate total pages
$totalPages = ceil($totalContracts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Contracts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            transition: transform 0.2s; /* Smooth scaling */
        }
        .card:hover {
            transform: scale(1.02); /* Scale up on hover */
        }
        .export-pdf-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            margin-top: 10px; /* Add margin to create space */
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<?php include('header.php'); ?>

<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary mb-4">List of Contracts</h1>

        <div class="row">
            <!-- Filter Section -->
            <div class="col-md-3 mb-4">
                <div class="card p-3 shadow-sm">
                    <form method="GET" action="">
                        <h5 class="card-title">Filter Contracts</h5>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">All</option>
                                <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="completed" <?= ($filters['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="canceled" <?= ($filters['status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?= $filters['start_date'] ?? ''; ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="<?= $filters['end_date'] ?? ''; ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="user_name">User Name:</label>
                            <input type="text" id="user_name" name="user_name" value="<?= $filters['user_name'] ?? ''; ?>" placeholder="Enter name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="vehicletitle">Vehicle Title:</label>
                            <input type="text" id="vehicletitle" name="vehicletitle" value="<?= $filters['vehicletitle'] ?? ''; ?>" placeholder="Enter vehicle title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="sort_by">Sort by:</label>
                            <select id="sort_by" name="sort_by" class="form-control">
                                <option value="">Select...</option>
                                <option value="start_date" <?= ($filters['sort_by'] === 'start_date') ? 'selected' : ''; ?>>Start Date</option>
                                <option value="end_date" <?= ($filters['sort_by'] === 'end_date') ? 'selected' : ''; ?>>End Date</option>
                                <option value="total_payment" <?= ($filters['sort_by'] === 'total_payment') ? 'selected' : ''; ?>>Total Payment</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="order">Order:</label>
                            <select id="order" name="order" class="form-control">
                                <option value="asc" <?= ($filters['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                                <option value="desc" <?= ($filters['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Contracts List -->
            <div class="col-md-9">
                <div class="row">
                    <?php foreach ($contracts as $contract): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card shadow-sm position-relative">
                                <!-- Image for the contract -->
                                <img src="uploads/contract.png" class="card-img-top" alt="Contract Image">

                                
                                <div class="card-body text-center">
                                    <!-- Card Title and Export Button with Spacing -->
                                    <h5 class="card-title text-primary d-flex justify-content-between align-items-center">
                                        Contract ID: <?= $contract['id']; ?>
                                        <a href="export_contract_pdf.php?id=<?= $contract['id']; ?>" class="btn btn-outline-success btn-sm export-pdf-btn ml-auto" style="margin-top: 10px;">Export PDF</a>

                                    </h5>
                                    
                                    <p class="card-text"><strong>User:</strong> <?= $contract['prenom'] . ' ' . $contract['nom']; ?></p>
                                    <p class="card-text"><strong>Car:</strong> <?= $contract['vehicletitle']; ?></p>
                                    <p class="card-text"><strong>Start Date:</strong> <?= $contract['start_date']; ?></p>
                                    <p class="card-text"><strong>End Date:</strong> <?= $contract['end_date']; ?></p>
                                    <p class="card-text"><strong>Total Payment:</strong> <?= $contract['total_payment']; ?> MAD</p>
                                    <p class="card-text">
                                        <strong>Status:</strong>
                                        <?php if ($contract['status'] === 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif ($contract['status'] === 'completed'): ?>
                                            <span class="badge badge-primary">Completed</span>
                                        <?php elseif ($contract['status'] === 'canceled'): ?>
                                            <span class="badge badge-danger">Canceled</span>
                                        <?php endif; ?>
                                    </p>

                                    <div class="btn-group mt-2">
                                        <a href="view_contract.php?id=<?= $contract['id']; ?>" class="btn btn-outline-info btn-sm">View</a>|
                                        <?php if ($contract['status'] === 'active' ): ?>
                                        <a href="update_contract.php?id=<?= $contract['id']; ?>" class="btn btn-outline-warning btn-sm">Edit</a>|
                                        <?php endif; ?><hr>
                                        <?php if ($contract['status'] === 'completed' || $contract['status'] === 'canceled' ): ?>
                                        <a href="delete_contract.php?id=<?= $contract['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this contract?');">Delete</a>
                                           <?php endif; ?><hr>
                                        <?php if ($contract['status'] === 'active'): ?>
                                            <a href="cancel_contract.php?id=<?= $contract['id']; ?>" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Are you sure you want to cancel this contract?');">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="add_contract.php" class="btn btn-primary">Add New Contract</a>
        </div>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
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
    </div>

<?php include('footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
