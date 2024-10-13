<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);
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
            background-color: #f1f3f5;
            color: #333;
        }
        .container1{
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        h1 {
            text-align: center;
            font-size: 32px;
            color: #343a40;
            margin-bottom: 40px;
        }
        .profile-card {
            max-width: 700px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .profile-card img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 3px solid #007BFF;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-card h2 {
            font-size: 26px;
            font-weight: 700;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .profile-card p {
            font-size: 18px;
            color: #495057;
            margin: 10px 0;
        }
        .profile-card p strong {
            color: #212529;
            font-weight: 700;
        }
        .info {
            margin-top: 20px;
            text-align: left;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        .info p {
            margin: 10px 0;
            font-size: 16px;
            color: #343a40;
        }
        .info p strong {
            display: inline-block;
            width: 140px;
            color: #495057;
        }
        .back-btn {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 30px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .back-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<?php include('header.php'); ?>

<body>
<div style="padding-top: 100px;">
    <div class="container">
        <h1>User Information</h1>
        <div class="profile-card">
            <?php if ($user): ?>
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= $user['photo']; ?>" alt="<?= $user['username']; ?>'s Profile Photo">
                <?php else: ?>
                    <img src="default-avatar.png" alt="Default Avatar">
                <?php endif; ?>

                <h2><?= $user['username']; ?></h2>
                
                <div class="info">
                    <p><strong>CIN:</strong> <?= $user['cin']; ?></p>
                    <p><strong>Email:</strong> <?= $user['email']; ?></p>
                    <p><strong>Role:</strong> <?= $user['role']; ?></p>
                    <p><strong>Nom:</strong> <?= $user['nom']; ?></p>
                    <p><strong>Prénom:</strong> <?= $user['prenom']; ?></p>
                    <p><strong>Date of Birth:</strong> <?= $user['date_of_birth']; ?></p>
                    <p><strong>Phone:</strong> <?= $user['numtelephone']; ?></p>
                </div>

                <a class="back-btn" href="user_list.php">Back to Users</a>
            <?php else: ?>
                <p>User not found.</p>
                <a class="back-btn" href="user_list.php">Back to Users</a>
            <?php endif; ?>
        </div>
    </div>
</body>
<?php include('footer.php'); ?>
</html>
