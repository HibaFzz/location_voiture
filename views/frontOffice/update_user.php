<?php
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);
include '../../controllers/UserController.php';

$userController = new UserController();
$errors = [];
$uploadOk = 1;

// Check if an ID is provided
if (isset($_GET['id'])) {
    // Fetch the user by ID
    $user = $userController->getUserById($_GET['id']);
    
    // If no user is found, redirect to the user list
    if (!$user) {
        header('Location: user_list.php'); // Redirect if not found
        exit();
    }
} else {
    die("User ID not provided.");
}

// Handle form submission
if (isset($_POST['submit'])) {
    $userId = $_POST['user_id'];
    $username = trim($_POST['username']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $numtelephone = trim($_POST['numtelephone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $cin = trim($_POST['cin']);
    
    // Validate input fields

    if (empty($nom)) $errors['nom'] = "First name is required.";
    if (empty($prenom)) $errors['prenom'] = "Last name is required.";
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "A valid email is required.";
    }
    
    // Validate phone number (must be 10 digits)
    if (empty($numtelephone)) {
        $errors['numtelephone'] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $numtelephone)) {
        $errors['numtelephone'] = "Phone number must be 10 digits.";
    }

    // Password validation: Only change password if fields are not empty
    if (!empty($password)) {
        if (empty($confirm_password)) {
            $errors['confirm_password'] = "Please confirm your password.";
        } elseif ($password !== $confirm_password) {
            $errors['confirm_password'] = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    if (empty($role)) $errors['role'] = "Role is required.";
    
    // Date of birth validation
    if (empty($date_of_birth)) {
        $errors['date_of_birth'] = "Date of birth is required.";
    } else {
        // Convert the date of birth input into a DateTime object
        $dob = new DateTime($date_of_birth);
        $today = new DateTime();
    
        // Calculate the age difference
        $age = $today->diff($dob)->y;
    
        // Validate if the age is greater than or equal to 20
        if ($age < 20) {
            $errors['date_of_birth'] = "You must be at least 20 years old.";
        }
    }

   // Original values from the database
$oldUsername = $user['username'];
$oldCin = $user['cin'];

// New values from the form submission
$username = $_POST['username'];
$cin = $_POST['cin'];

$errors = [];

// Username validation
if (empty($username)) {
    $errors['username'] = "Username is required.";
} elseif ($username !== $oldUsername && $userController->usernameExists($username)) {
    $errors['username'] = "Username already exists.";
}

// CIN validation
if (empty($cin) || !is_numeric($cin)) {
    $errors['cin'] = "A valid CIN is required.";
} elseif ($cin !== $oldCin && $userController->cinExists($cin)) {
    $errors['cin'] = "CIN already exists.";
}


    // Handle photo upload
    $target_file = $user['photo']; // Default to existing photo if none is uploaded
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image upload
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check === false) {
            $errors['photo'] = "File is not an image.";
            $uploadOk = 0;
        } elseif ($_FILES["photo"]["size"] > 500000) {
            $errors['photo'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['photo'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        } elseif (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $errors['photo'] = "There was an error uploading your photo.";
            $uploadOk = 0;
        }
    }

    // If no errors, update user information
    if (empty($errors)) {
        $userController->updateUser(
            $userId,
            $username,
            $nom,
            $prenom,
            !empty($password) ? $hashed_password : $user['password'], // Use hashed password if provided, else keep old password
            $email,
            $numtelephone,
            $role,
            $date_of_birth,
            $cin,
            $target_file
        );
        header('Location: user_list.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f7f9fc; color: #333; margin: 0;  }
        h2 { text-align: center; color: #007bff; font-size: 2em; margin-bottom: 20px; }
        form { background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); margin: auto; max-width: 900px; }
        .form-section { display: flex; justify-content: space-between; margin: 20px 0; }
        .section { flex: 1; min-width: 300px; max-width: 400px; padding: 0 10px; }
        .section-title { font-size: 1.5em; color: #007bff; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="date"], .form-group select, .form-group input[type="file"] { width: 100%; padding: 8px; border: 1px solid #007bff; border-radius: 4px; }
        input[type="submit"] { background-color: #007bff; color: white; border: none; border-radius: 4px; padding: 10px 20px; cursor: pointer; font-size: 16px; margin-top: 10px; width: 100%; }
        input[type="submit"]:hover { background-color: #0056b3; }
        .return-link { text-align: center; margin-top: 20px; font-size: 1.2em; }
        .return-link a { color: #007bff; text-decoration: none; }
        .return-link a:hover { text-decoration: underline; }
        .error-message { color: red; margin-top: 5px; display: block; }
        .current-photo { margin-bottom: 15px; }
        .current-photo img { max-width: 150px; max-height: 150px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        @media (max-width: 600px) { .form-section { flex-direction: column; } }
    </style>
</head>
<body>
<div style="padding-top: 100px;">
    <h2>Update User</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">

        <div class="form-section">
            <div class="section">
                <div class="section-title">User Information</div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" value="<?= $user['username']; ?>">
                    <?php if (!empty($errors['username'])): ?>
                        <div class="error-message"><?= $errors['username']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="nom">First Name:</label>
                    <input type="text" name="nom" id="nom" value="<?= $user['nom']; ?>">
                    <?php if (!empty($errors['nom'])): ?>
                        <div class="error-message"><?= $errors['nom']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="prenom">Last Name:</label>
                    <input type="text" name="prenom" id="prenom" value="<?= $user['prenom']; ?>">
                    <?php if (!empty($errors['prenom'])): ?>
                        <div class="error-message"><?= $errors['prenom']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?= $user['email']; ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error-message"><?= $errors['email']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="photo">Upload New Photo:</label>
                    <input type="file" name="photo" id="photo">
                    <?php if (!empty($errors['photo'])): ?>
                        <div class="error-message"><?= $errors['photo']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="current-photo">
                    <?php if (!empty($user['photo'])): ?>
                        <p>Current Photo:</p>
                        <img src="<?= $user['photo']; ?>" alt="User Photo">
                    <?php endif; ?>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Security & Role</div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password">
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <div class="error-message"><?= $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" id="role">
                        <option value="">--Select Role--</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
                        <option value="client" <?= $user['role'] === 'client' ? 'selected' : ''; ?>>Client</option>
                    </select>
                    <?php if (!empty($errors['role'])): ?>
                        <div class="error-message"><?= $errors['role']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="cin">CIN:</label>
                    <input type="text" name="cin" id="cin" value="<?= $user['cin']; ?>">
                    <?php if (!empty($errors['cin'])): ?>
                        <div class="error-message"><?= $errors['cin']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="<?= $user['date_of_birth']; ?>">
                    <?php if (!empty($errors['date_of_birth'])): ?>
                        <div class="error-message"><?= $errors['date_of_birth']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="numtelephone">Phone Number:</label>
                    <input type="text" name="numtelephone" id="numtelephone" value="<?= $user['numtelephone']; ?>">
                    <?php if (!empty($errors['numtelephone'])): ?>
                        <div class="error-message"><?= $errors['numtelephone']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <input type="submit" name="submit" value="Update User">
    </form>

    <div class="return-link">
        <a href="user_list.php">Back to User List</a>
    </div>
</div>


<?php include('footer.php'); ?>
</body>
</html>
