<?php
include '../../controllers/UserController.php';

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

// Use filterUsers method
$users = $userController->filterUsers($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Your existing styles */
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
            gap: 20px;
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
        .filter-card select, .filter-card input[type="text"], .filter-card input[type="submit"], .filter-card input[type="date"] {
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
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            flex: 3;
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
            transform: translateY(-5px);
        }
        .card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
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
    <h1>List of Users</h1>

    <div class="container">
        
        <div class="filter-card">
            <form method="GET" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($filters['username']); ?>">

                <label for="cin">CIN:</label>
                <input type="text" id="cin" name="cin" value="<?= htmlspecialchars($filters['cin']); ?>">

                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="">All</option>
                    <option value="admin" <?= ($filters['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="agent" <?= ($filters['role'] === 'agent') ? 'selected' : ''; ?>>Agent</option>
                    <option value="client" <?= ($filters['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                </select>

                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($filters['date_of_birth']); ?>">

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

                <input type="submit" value="Filter">
            </form>
        </div>

        <div class="card-container">
            <?php foreach ($users as $user): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($user['username']); ?></h2>
                    <div>
                        <?php if (!empty($user['photo'])): ?>
                            <img src="<?= htmlspecialchars($user['photo']); ?>" alt="User Photo">
                        <?php else: ?>
                            <p>No photo available</p>
                        <?php endif; ?>
                    </div>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['numtelephone']); ?></p>
                    <div class="actions">
                        <a href="view_user.php?id=<?= htmlspecialchars($user['id']); ?>">View</a> |
                        <a href="update_user.php?id=<?= htmlspecialchars($user['id']); ?>">Edit</a> |
                        <a href="delete_user.php?id=<?= htmlspecialchars($user['id']); ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <br>
    <a href="add_user.php" style="display: block; text-align: center; background-color: #007BFF; color: white; padding: 10px; border-radius: 5px; text-decoration: none; width: 150px; margin: 20px auto;">Add New User</a>
</body>
</html>
