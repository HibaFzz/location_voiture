<?php
include '../../controllers/UserController.php';

$userController = new UserController();

// Get user ID from the URL
$userId = $_GET['id'] ?? null;

if ($userId) {
    // Get the user by ID
    $user = $userController->getUserById($userId);
} else {
    // Redirect if no user ID is provided
    header('Location: user_list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .profile-card {
            max-width: 500px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .profile-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .profile-card h2 {
            font-size: 24px;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .profile-card p {
            color: #555;
            margin: 5px 0;
        }
        .profile-card .info {
            margin: 20px 0;
            text-align: left;
        }
        .info strong {
            color: #333;
        }
        .back-btn {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="profile-card">
        <?php if ($user): ?>
            <?php if (!empty($user['photo'])): ?>
                <img src="<?= $user['photo']; ?>" alt="<?= $user['username']; ?>'s Profile Photo">
            <?php else: ?>
                <img src="default-avatar.png" alt="Default Avatar">
            <?php endif; ?>

            <h2><?= $user['username']; ?></h2>
            <p><strong>Email:</strong> <?= $user['email']; ?></p>
            <p><strong>Role:</strong> <?= $user['role']; ?></p>

            <div class="info">
                <p><strong>CIN:</strong> <?= $user['cin']; ?></p>
                <p><strong>Date of Birth:</strong> <?= $user['date_of_birth']; ?></p>
                <p><strong>Phone:</strong> <?= $user['numtelephone']; ?></p>
            </div>

            <a class="back-btn" href="user_list.php">Back to Users</a>
        <?php else: ?>
            <p>User not found.</p>
            <a class="back-btn" href="user_list.php">Back to Users</a>
        <?php endif; ?>
    </div>

</body>
</html>
