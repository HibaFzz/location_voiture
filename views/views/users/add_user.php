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
        $userController->addUser($username, $nom, $prenom, $email, $numtelephone, $password, $role, $date_of_birth, $cin, $target_file);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
</head>
<body>
    <h1>Add New User</h1>

    <!-- Display errors if any exist -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to add a new user -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label for="nom">First Name:</label><br> <!-- Added first name input -->
        <input type="text" name="nom" id="nom" required><br><br>

        <label for="prenom">Last Name:</label><br> <!-- Added last name input -->
        <input type="text" name="prenom" id="prenom" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="numtelephone">Phone Number:</label><br>
        <input type="text" name="numtelephone" id="numtelephone"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <label for="role">Role:</label><br>
        <select name="role" id="role" required> <!-- Added dropdown for roles -->
            <option value="admin">Admin</option>
            <option value="agent">Agent</option>
            <option value="client">Client</option>
        </select><br><br>

        <label for="date_of_birth">Date of Birth:</label><br>
        <input type="date" name="date_of_birth" id="date_of_birth" required><br><br>

        <label for="cin">CIN:</label><br>
        <input type="text" name="cin" id="cin" required><br><br>

        <label for="photo">Profile Photo (Optional):</label><br>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>

        <input type="submit" name="submit" value="Add User">
    </form>
    <br>
    <a href="user_list.php">Back to User List</a>
</body>
</html>
