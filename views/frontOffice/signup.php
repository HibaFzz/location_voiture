<?php 
include '../../controllers/UserController.php';

$userController = new UserController();
$errors = []; 
$uploadOk = 1;

if (isset($_POST['submit'])) {

    $username = trim(string: $_POST['username']);
    $nom = trim(string: $_POST['nom']);
    $prenom = trim(string: $_POST['prenom']);
    $email = trim(string: $_POST['email']);
    $numtelephone = trim(string: $_POST['numtelephone']);
    $password = trim(string: $_POST['password']);
    $confirm_password = trim(string: $_POST['confirm_password']);
    $role = trim(string: $_POST['role']);
    $date_of_birth = trim(string: $_POST['date_of_birth']);
    $cin = trim(string: $_POST['cin']);

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif ($userController->usernameExists(username: $username)) {
        $errors['username'] = "Username already exists.";
    }


    if (empty($nom)) {
        $errors['nom'] = "First name is required.";
    }


    if (empty($prenom)) {
        $errors['prenom'] = "Last name is required.";
    }


    if (empty($email) || !filter_var(value: $email, filter: FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "A valid email is required.";
    }

    if (empty($numtelephone) || !preg_match(pattern: '/^[0-9]{10}$/', subject: $numtelephone)) {
        $errors['numtelephone'] = "A valid phone number (10 digits) is required.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }


    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }


    if (empty($role)) {
        $errors['role'] = "Role is required.";
    }

    if (empty($date_of_birth)) {
        $errors['date_of_birth'] = "Date of birth is required.";
    } else {
 
        $dob = new DateTime(datetime: $date_of_birth);
        $today = new DateTime();

        $age = $today->diff(targetObject: $dob)->y;
    

        if ($age < 20) {
            $errors['date_of_birth'] = "You must be at least 20 years old.";
        }
    }

    if (empty($cin) || !is_numeric(value: $cin)) {
        $errors['cin'] = "A valid CIN is required.";
    } elseif ($userController->cinExists(cin: $cin)) {
        $errors['cin'] = "CIN already exists.";
    }

    $target_file = "";
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename(path: $_FILES["photo"]["name"]);
        $imageFileType = strtolower(string: pathinfo(path: $target_file, flags: PATHINFO_EXTENSION));

        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $errors['photo'] = "Image upload failed with error code: " . $_FILES["photo"]["error"];
            $uploadOk = 0;
        } else {
            $check = getimagesize(filename: $_FILES["photo"]["tmp_name"]);
            if ($check === false) {
                $errors['photo'] = "File is not an image.";
                $uploadOk = 0;
            }
        }

        if ($_FILES["photo"]["size"] > 500000) {
            $errors['photo'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array(needle: $imageFileType, haystack: ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['photo'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1 && empty($errors)) {
            if (!is_dir(filename: $target_dir)) {
                mkdir(directory: $target_dir, permissions: 0755, recursive: true);
            }

            if (!move_uploaded_file(from: $_FILES["photo"]["tmp_name"], to: $target_file)) {
                $errors['photo'] = "Sorry, there was an error uploading your file.";
            }
        }
    }

if (empty($errors)) {
    $hashed_password = password_hash(password: $password, algo: PASSWORD_DEFAULT);
    $userController->signUp(username: $username, nom: $nom, prenom: $prenom, email: $email, numtelephone: $numtelephone, password: $hashed_password, role: $role, date_of_birth: $date_of_birth, cin: $cin, photo: $target_file);
    
    header(header: 'Location: login.php');
    exit();
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Car Rental</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #003366, #4a90e2);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('car-background.jpg') no-repeat center center/cover;
            opacity: 0.3;
            z-index: -1;
        }

        .signup-container {
            background-color: rgba(0, 51, 102, 0.85);
            padding: 40px;
            border-radius: 12px;
            width: 900px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        h2 {
            margin-bottom: 25px;
            font-size: 28px;
            color: #fff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-section {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .form-section h3 {
            font-size: 18px;
            color: #dcdcdc;
            margin-bottom: 20px;
            border-bottom: 1px solid #4a90e2;
            padding-bottom: 10px;
            text-align: left;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .input-group label {
            margin-bottom: 5px;
            font-size: 16px;
            color: #dcdcdc;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .input-group input:hover, .input-group select:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #357abd;
        }

        .error {
            color: red;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <h2>Sign Up</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- User Details Section -->
            <div class="form-section">
                <h3>Personal Information</h3>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
                    <?php if (isset($errors['username'])): ?>
                        <span class="error"><?= $errors['username'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="nom">First Name</label>
                    <input type="text" id="nom" name="nom" value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">
                    <?php if (isset($errors['nom'])): ?>
                        <span class="error"><?= $errors['nom'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="prenom">Last Name</label>
                    <input type="text" id="prenom" name="prenom" value="<?= isset($prenom) ? htmlspecialchars($prenom) : '' ?>">
                    <?php if (isset($errors['prenom'])): ?>
                        <span class="error"><?= $errors['prenom'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="numtelephone">Phone Number</label>
                    <input type="text" id="numtelephone" name="numtelephone" value="<?= isset($numtelephone) ? htmlspecialchars($numtelephone) : '' ?>">
                    <?php if (isset($errors['numtelephone'])): ?>
                        <span class="error"><?= $errors['numtelephone'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?= isset($date_of_birth) ? htmlspecialchars($date_of_birth) : '' ?>">
                    <?php if (isset($errors['date_of_birth'])): ?>
                        <span class="error"><?= $errors['date_of_birth'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Details Section -->
            <div class="form-section">
                <h3>Account Information</h3>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?= $errors['confirm_password'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="role">Role</label>
                    <select name="role" id="role">
                            <option value="admin" <?php echo (isset($role) && $role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="agent" <?php echo (isset($role) && $role == 'agent') ? 'selected' : ''; ?>>Agent</option>
                            <option value="client" <?php echo (isset($role) && $role == 'client') ? 'selected' : ''; ?>>Client</option>
                        </select>
                    <?php if (isset($errors['role'])): ?>
                        <span class="error"><?= $errors['role'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="cin">CIN</label>
                    <input type="text" id="cin" name="cin" value="<?= isset($cin) ? htmlspecialchars($cin) : '' ?>">
                    <?php if (isset($errors['cin'])): ?>
                        <span class="error"><?= $errors['cin'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="photo">Photo</label>
                    <input type="file" id="photo" name="photo">
                    <?php if (isset($errors['photo'])): ?>
                        <span class="error"><?= $errors['photo'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="btn">Sign Up</button>
    </form>
</div>

</body>
</html>
