<?php 
include '../../controllers/UserController.php'; // Correct path for the controller
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['admin']);
$userController = new UserController();
$errors = []; // Array to hold errors
$uploadOk = 1; // Variable for file upload control

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    // Get the form values
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

    // Username validation (unique)
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif ($userController->usernameExists($username)) {
        $errors['username'] = "Username already exists.";
    }

    // First name validation
    if (empty($nom)) {
        $errors['nom'] = "First name is required.";
    }

    // Last name validation
    if (empty($prenom)) {
        $errors['prenom'] = "Last name is required.";
    }

    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "A valid email is required.";
    }

    // Phone number validation (10 digits)
    if (empty($numtelephone) || !preg_match('/^[0-9]{10}$/', $numtelephone)) {
        $errors['numtelephone'] = "A valid phone number (10 digits) is required.";
    }

    // Password validation
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // Password confirmation validation
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Role validation
    if (empty($role)) {
        $errors['role'] = "Role is required.";
    }

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

    // CIN validation (unique)
    if (empty($cin) || !is_numeric($cin)) {
        $errors['cin'] = "A valid CIN is required.";
    } elseif ($userController->cinExists($cin)) {
        $errors['cin'] = "CIN already exists.";
    }

    // Photo upload handling
    $target_file = "";
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Image validation
        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $errors['photo'] = "Image upload failed with error code: " . $_FILES["photo"]["error"];
            $uploadOk = 0;
        } else {
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if ($check === false) {
                $errors['photo'] = "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Other image validations (size, format)
        if ($_FILES["photo"]["size"] > 500000) {
            $errors['photo'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['photo'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        // If no error, move the file
        if ($uploadOk == 1 && empty($errors)) {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $errors['photo'] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // If no errors, add the user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $userController->addUser($username, $nom, $prenom, $email, $numtelephone, $hashed_password, $role, $date_of_birth, $cin, $target_file);

        // Redirect after successful insertion
        header('Location: user_list.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('index.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>>Add New User</title>

    <style>
        /* CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Flexbox layout for the whole page */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

       
        /* Main content form styling */
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-size: 2em;
            margin-bottom: 20px;
        }

        .form-section {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .section {
            flex: 1;
            min-width: 300px;
            max-width: 400px;
            padding: 0 10px;
        }

        .section-title {
            font-size: 1.5em;
            color: #007bff;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"], 
        .form-group input[type="email"], 
        .form-group input[type="password"], 
        .form-group input[type="date"], 
        .form-group select, 
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .return-link {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
        }

        .return-link a {
            color: #007bff;
            text-decoration: none;
        }

        .return-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-top: 5px;
            display: block;
        }

        /* Footer section */
        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            color: #333;
            border-top: 1px solid #ddd;
            width: 100%;
            position: relative;
            bottom: 0;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 600px) {
            .form-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container1">
    <div style="padding-top: 100px;">
        <h2>Add New User</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= $user['id']; ?>">

            <div class="form-section">
                <div class="section">
                    <div class="section-title">User Information</div>

                    <div class="form-group">
                        <label for="cin">CIN:</label>
                        <input type="text" name="cin" id="cin" value="<?php echo $cin ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['cin'])): ?>
                                <span><?php echo $errors['cin']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nom">First Name:</label>
                        <input type="text" name="nom" id="nom" value="<?php echo $nom ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['nom'])): ?>
                                <span><?php echo $errors['nom']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Last Name:</label>
                        <input type="text" name="prenom" id="prenom" value="<?php echo $prenom ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['prenom'])): ?>
                                <span><?php echo $errors['prenom']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="numtelephone">Phone Number:</label>
                        <input type="text" name="numtelephone" id="numtelephone" value="<?php echo $numtelephone ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['numtelephone'])): ?>
                                <span><?php echo $errors['numtelephone']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth:</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo $date_of_birth ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <span><?php echo $errors['date_of_birth']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo">Profile Picture:</label>
                        <input type="file" name="photo" id="photo">
                        <div class="error-message">
                            <?php if (isset($errors['photo'])): ?>
                                <span><?php echo $errors['photo']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">Security & Role</div>

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo $username ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['username'])): ?>
                                <span><?php echo $errors['username']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password">
                        <div class="error-message">
                            <?php if (isset($errors['password'])): ?>
                                <span><?php echo $errors['password']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password">
                        <div class="error-message">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <span><?php echo $errors['confirm_password']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email" value="<?php echo $email ?? ''; ?>">
                        <div class="error-message">
                            <?php if (isset($errors['email'])): ?>
                                <span><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select name="role" id="role">
                            <option value="admin" <?php echo (isset($role) && $role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="agent" <?php echo (isset($role) && $role == 'agent') ? 'selected' : ''; ?>>Agent</option>
                            <option value="client" <?php echo (isset($role) && $role == 'client') ? 'selected' : ''; ?>>Client</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <input type="submit" name="submit" value="Add User">
        </form>

        <div class="return-link">
            <a href="user_list.php">Back to User List</a>
        </div>
    </div>
</body>
</html>
