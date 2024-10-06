<?php
include '../../controllers/UserController.php'; // Ensure this path is correct

$userController = new UserController();
$errors = []; // Initialize an array to hold error messages
$uploadOk = 1; // Variable to track if the upload should proceed

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get input values
    $userId = $_POST['user_id']; // Assume the user ID is passed via a hidden field
    $username = trim($_POST['username']);
    $nom = trim($_POST['nom']); // Added nom
    $prenom = trim($_POST['prenom']); // Added prenom
    $email = trim($_POST['email']);
    $numtelephone = trim($_POST['numtelephone']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $cin = trim($_POST['cin']);
    
    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Validate nom
    if (empty($nom)) {
        $errors[] = "Nom is required.";
    }

    // Validate prenom
    if (empty($prenom)) {
        $errors[] = "Prenom is required.";
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
    $target_file = ""; // Default to an empty string
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

    // If there are no errors, proceed to update the user
    if (empty($errors)) {
        // Update the user in the database
        $userController->updateUser($userId, $username, $nom, $prenom, $email, $numtelephone, $password, $role, $date_of_birth, $cin, $target_file);

        // Redirect to the list of users after successful update
        header('Location: user_list.php');
        exit();
    }
}

// Fetch the user data for the form (assuming user ID is passed in the URL)
if (isset($_GET['id'])) {
    $user = $userController->getUserById($_GET['id']); // Fetch user data from the controller
}

// Display errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>
    <h1>Update User</h1>

    <!-- Display errors if any exist -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to update user -->
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">

        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" value="<?= $user['username']; ?>" required><br><br>

        <label for="nom">Nom:</label><br>
        <input type="text" name="nom" id="nom" value="<?= $user['nom']; ?>" required><br><br>

        <label for="prenom">Prenom:</label><br>
        <input type="text" name="prenom" id="prenom" value="<?= $user['prenom']; ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" value="<?= $user['email']; ?>" required><br><br>

        <label for="numtelephone">Phone Number:</label><br>
        <input type="text" name="numtelephone" id="numtelephone" value="<?= $user['numtelephone']; ?>"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <label for="role">Role:</label><br>
        <select name="role" id="role" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Client</option>
            <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : '' ?>>Agent</option>
        </select><br><br>

        <label for="date_of_birth">Date of Birth:</label><br>
        <input type="date" name="date_of_birth" id="date_of_birth" value="<?= $user['date_of_birth']; ?>" required><br><br>

        <label for="cin">CIN:</label><br>
        <input type="text" name="cin" id="cin" value="<?= $user['cin']; ?>" required><br><br>

        <label for="photo">Profile Photo (Optional):</label><br>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>

        <input type="submit" name="submit" value="Update User">
    </form>
    <br>
    <a href="user_list.php">Back to User List</a>
</body>
</html>
