<?php 
include '../../controllers/UserController.php'; // Ensure this path is correct

$userController = new UserController();
$errors = []; // Initialize an array to hold error messages
$uploadOk = 1; // Variable to track if the upload should proceed

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get input values
    $username = trim($_POST['username']);
    $nom = trim($_POST['nom']); // Added first name
    $prenom = trim($_POST['prenom']); // Added last name
    $email = trim($_POST['email']);
    $numtelephone = trim($_POST['numtelephone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']); // Added confirm password
    $role = trim($_POST['role']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $cin = trim($_POST['cin']);
    
    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Validate first name
    if (empty($nom)) {
        $errors[] = "First name is required.";
    }

    // Validate last name
    if (empty($prenom)) {
        $errors[] = "Last name is required.";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    // Validate phone number (assuming it should be numeric)
    if (empty($numtelephone) || !preg_match('/^[0-9]{10}$/', $numtelephone)) {
        $errors[] = "A valid phone number (10 digits) is required.";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Validate password confirmation
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Hash the password before saving it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Validate role
    if (empty($role)) {
        $errors[] = "Role is required.";
    }

    // Validate date of birth
    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required.";
    }

    // Validate CIN
    if (empty($cin) || !is_numeric($cin)) {
        $errors[] = "A valid CIN is required.";
    }

    // Handle the photo upload if a file is uploaded
    $target_file = "";
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate the image
        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Image upload failed with error code: " . $_FILES["photo"]["error"];
            $uploadOk = 0;
        } else {
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if ($check === false) {
                $errors[] = "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Other validations (size, format, etc.)
        if ($_FILES["photo"]["size"] > 500000) {
            $errors[] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        // If no errors, move the uploaded file
        if ($uploadOk == 1 && empty($errors)) {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // If there are no errors, proceed to add the user
    if (empty($errors)) {
        // Add the user to the database
        $userController->addUser($username, $nom, $prenom, $email, $numtelephone, $hashed_password, $role, $date_of_birth, $cin, $target_file);

        // Redirect to the list of users after successful insertion
        header('Location: user_list.php');
        exit();
    }
}

// Display errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>" . htmlspecialchars($error) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-size: 2em;
            margin-bottom: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px;
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
            display: flex;
            margin-bottom: 15px;
        }

        .form-group label {
            flex-basis: 35%;
            margin-right: 10px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group select,
        .form-group input[type="file"] {
            flex-basis: 60%;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: border-color 0.3s;
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

        @media (max-width: 600px) {
            .form-section {
                flex-direction: column;
            }

            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group label {
                margin-bottom: 5px;
            }

            input[type="submit"] {
                width: auto;
            }
        }
    </style>
</head>
<body>
<div style="padding-top: 100px;">
    <h2>Add A New User</h2>

    <!-- Display errors if any exist -->
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to add a new user -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <div class="section">
                <div class="section-title">User Information</div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-group">
                    <label for="nom">First Name:</label>
                    <input type="text" name="nom" id="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Last Name:</label>
                    <input type="text" name="prenom" id="prenom" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="numtelephone">Phone Number:</label>
                    <input type="text" name="numtelephone" id="numtelephone" required>
                </div>
                <div class="form-group">
                    <label for="photo">Profile Photo (Optional):</label>
                    <input type="file" name="photo" id="photo" accept="image/*">
                </div>
            </div>

            <div class="section">
                <div class="section-title">Account Settings</div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" id="role" required>
                        <option value="admin">Admin</option>
                        <option value="agent">Agent</option>
                        <option value="client">Client</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="cin">CIN:</label>
                    <input type="text" name="cin" id="cin" required>
                </div>

            </div>
        </div>

        <input type="submit" name="submit" value="Add User">
    </form>

    <!-- Return to list of users link -->
    <div class="return-link">
        <a href="user_list.php">Back to User List</a>
    </div>
</body>
</html>
