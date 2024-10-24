<?php

include '../../controllers/UserController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['admin']);

$userController = new UserController();
$filters = [
    'username' => $_GET['username'] ?? '',
    'nom' => $_GET['nom'] ?? '',
    'prenom' => $_GET['prenom'] ?? '',
    'role' => $_GET['role'] ?? '',
    'cin' => $_GET['cin'] ?? '',
    'email' => $_GET['email'] ?? '',
    'date_of_birth' => $_GET['date_of_birth'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? '',
    'order' => $_GET['order'] ?? 'asc'
];

$limit = 9;
$page = $_GET['page'] ?? 1; // Get the current page or set to 1 if not defined
$offset = ($page - 1) * $limit; // Calculate offset for SQL query
// Use filterUsers method
$users = $userController->filterUsers($filters, $limit, $offset);
$totalUsers = $userController->getTotalUsers($filters);
// Calculate total pages for pagination
$totalPages = ceil($totalUsers / $limit);
echo "Total Users: " . $totalUsers . "\n";
foreach ($users as $user) {
    echo "User: " . $user['nom'] . "\n";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Users</title>

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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px; /* Reduced gap for better spacing */
            width: 100%;
            padding: 10px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
            padding: 10px; /* Reduced padding for less unused space */
            display: flex;
            flex-direction: column; /* Ensure content stacks vertically */
            justify-content: space-between; /* Spread out the content */
            height: 100%; /* Allow the card to fill the available height */
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 60%; /* Increased width for better visibility */
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .card h2 {
            font-size: 18px; /* Slightly smaller font size */
            margin: 10px 0;
            color: #007BFF;
        }
        .card p {
            color: #555;
            margin: 5px 0;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .actions button {
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .filter-card {
                max-width: none;
            }
        }
        
    </style>
</head>
<body>
<?php include('index.php'); ?>
<div style="padding-top: 100px;">
<h1 class="text-center text-primary mb-4">List of Users</h1>

    <div class="container">
        
        <div class="filter-card">
            <form method="GET" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= $filters['username']; ?>">

                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?= $filters['nom']; ?>" pattern="[A-Za-zÀ-ÿ\s]+" title="Please enter letters only.">

                <label for="prenom">Prenom:</label>
                <input type="text" id="prenom" name="prenom" value="<?= $filters['prenom']; ?>" pattern="[A-Za-zÀ-ÿ\s]+" title="Please enter letters only.">

                <label for="cin">CIN:</label>
                <input type="text" id="cin" name="cin" value="<?= $filters['cin']; ?>" pattern="\d+" title="Please enter numeric values only.">

                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="">All</option>
                    <option value="admin" <?= ($filters['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="agent" <?= ($filters['role'] === 'agent') ? 'selected' : ''; ?>>Agent</option>
                    <option value="client" <?= ($filters['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                </select>

                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= $filters['date_of_birth']; ?>">

                <label for="sort_by">Sort by:</label>
                <select id="sort_by" name="sort_by">
                    <option value="">Select...</option>
                    <option value="username" <?= ($filters['sort_by'] === 'username') ? 'selected' : ''; ?>>Username</option>
                    <option value="nom" <?= ($filters['sort_by'] === 'nom') ? 'selected' : ''; ?>>Nom</option>
                    <option value="prenom" <?= ($filters['sort_by'] === 'prenom') ? 'selected' : ''; ?>>Prenom</option>
                    <option value="role" <?= ($filters['sort_by'] === 'role') ? 'selected' : ''; ?>>Role</option>
                    <option value="date_of_birth" <?= ($filters['sort_by'] === 'date_of_birth') ? 'selected' : ''; ?>>Date of Birth</option>
                </select>

                <label for="order">Order:</label>
                <select id="order" name="order">
                    <option value="asc" <?= ($filters['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?= ($filters['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>

                <input type="submit" value="Filter" class="btn btn-primary" style="margin-top: 10px;">
            </form>
        </div>

        <div class="card-container">
            <?php foreach ($users as $user): ?>
                <div class="card">
                    <h2><?= $user['username']; ?></h2>
                    <div>
                        <?php if (!empty($user['photo'])): ?>
                            <img src="<?= $user['photo']; ?>" alt="User Photo">
                        <?php else: ?>
                            <p>No photo available</p>
                        <?php endif; ?>
                    </div>
                    <p><strong>Name:</strong> <?= $user['nom'] . ' ' . $user['prenom']; ?></p>
                    <p><strong>Role:</strong>
                    <span class="badge 
                        <?= $user['role'] === 'admin' ? 'badge-danger' : 
                            ($user['role'] === 'agent' ? 'badge-primary' : 
                            ($user['role'] === 'client' ? 'badge-success' : 'badge-warning')) ?>"
                        style="background-color: 
                        <?= $user['role'] === 'admin' ? '#dc3545' : 
                            ($user['role'] === 'agent' ? '#007bff' : 
                            ($user['role'] === 'client' ? '#28a745' : '#ffc107')) ?>;">
                        <?= ucfirst($user['role']); ?>
                    </span>



                    <div class="actions">
                        <a href="view_user.php?id=<?= $user['id']; ?>" class="btn btn-info">View</a>
                        <a href="update_user.php?id=<?= $user['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_user.php?id=<?= $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <nav>
            <ul class="pagination">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>&<?= http_build_query($filters); ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i === (int)$page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>&<?= http_build_query($filters); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>&<?= http_build_query($filters); ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
    <a href="add_user.php" style="display: block; text-align: center; background-color: #007BFF; color: white; padding: 10px; border-radius: 5px; text-decoration: none; width: 150px; margin: 20px auto;">Add New User</a>
    </div>

</div>
</body>
</html>
