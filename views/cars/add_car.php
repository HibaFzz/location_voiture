<?php
include '../../controllers/CarController.php'; // Ensure this path is correct

$carsController = new CarController();
$errors = []; // Initialize an array to hold error messages
$uploadOk = 1; // Variable to track if the upload should proceed

// Define your brands and their respective models
$brandsWithModels = [
    "STAFIM" => ["Renault"],
    "TAMC" => ["Various local assembly"],
    "Renault" => ["Clio", "Megane", "Duster"],
    "Peugeot" => ["208", "301", "308", "2008", "3008"],
    "Citroën" => ["C3", "C4", "C5"],
    "Volkswagen" => ["Golf", "Polo", "Tiguan"],
    "Fiat" => ["Tipo", "500", "Panda"],
    "Toyota" => ["Corolla", "Hilux", "Yaris"],
    "Kia" => ["Picanto", "Sportage", "Cerato"],
    "Hyundai" => ["i10", "i20"],
    "Nissan" => ["Micra", "Qashqai", "X-Trail"],
    "BMW" => ["Series 3", "Series 5", "X3"],
    "Mercedes-Benz" => ["C-Class", "E-Class", "GLC"],
    "Audi" => ["A3", "A4", "Q5"],
    "Dacia" => ["Sandero", "Duster"],
    "MG" => ["MG3", "MG ZS"]
];

// Define fuel types
$fuelTypes = ['essence', 'diesel'];

// Define model years (assuming from 2000 to current year)
$currentYear = date("Y");
$modelYears = range(2000, $currentYear);

// Define number of persons options
$personOptions = range(1, 10); // 1 to 10 persons

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get input values
    $matricule = trim($_POST['matricule']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']); // Get selected model
    $vehicleoverview = trim($_POST['vehicleoverview']);
    $priceperday = trim($_POST['priceperday']);
    $fueltype = trim($_POST['fueltype']);
    $modelyear = trim($_POST['modelyear']);
    $nbrpersonne = trim($_POST['nbrpersonne']);
    $disponible = trim($_POST['disponible']); // Get the disponible value

    // Assume a default image path (this could be retrieved from your database)
    $target_file = ""; // Initialize the image path

    // Validate input fields
    $matriculePattern = '/^\d{3}TUN\d{4}$/'; // Pattern for matricule (111TUN1111 format)
    if (empty($matricule)) {
        $errors[] = "Matricule is required.";
    } elseif (!preg_match($matriculePattern, $matricule)) {
        $errors[] = "Matricule must be in the format 111TUN1111.";
    }

    if (empty($brand)) {
        $errors[] = "Brand is required.";
    }

    if (empty($model)) {
        $errors[] = "Model is required.";
    }

    if (empty($priceperday) || !is_numeric($priceperday)) {
        $errors[] = "Valid price per day is required.";
    }

    if (empty($fueltype)) {
        $errors[] = "Fuel type is required.";
    }

    if (empty($modelyear)) {
        $errors[] = "Model year is required.";
    }

    if (empty($nbrpersonne) || !is_numeric($nbrpersonne)) {
        $errors[] = "Number of persons must be a valid number.";
    }

    // Handle the image only if a new one is uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        // Only process the upload if a new file is provided
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate the image
        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Image upload failed with error code: " . $_FILES["image"]["error"];
            $uploadOk = 0;
        } else {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                $errors[] = "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Other validations (size, format, etc.)
        if ($_FILES["image"]["size"] > 500000) {
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

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File successfully uploaded
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // No new file uploaded, retain the existing file path (from the database)
        // Retrieve the existing image path from the database if needed
        $target_file = $carsController->getCarImagePathByMatricule($matricule);
    }

    // If there are no errors, proceed to add or update the car
    if (empty($errors)) {
        // Combine brand and model for the database entry
        $vehicleTitle = $brand . ' ' . $model; // Automatically combine brand and model

        // Insert or update the car
        $carsController->addCar($matricule, $target_file, $vehicleTitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible); // Pass disponible

        // Redirect to the list of cars after successful insertion
        header('Location: list_cars.php');
        exit();
    }
}

// Display errors (if any)
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>" . htmlspecialchars($error) . "</p>"; // Escape output for safety
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Car</title>
</head>
<body>
    <h1>Add New Car</h1>

    <!-- Display errors if any exist -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p> <!-- Escape output for safety -->
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to add a new car -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="matricule">Matricule (Format: 111TUN1111):</label><br>
        <input type="text" name="matricule" id="matricule" required><br><br>

        <label for="image">Image (Optional):</label><br>
        <input type="file" name="image" id="image" accept="image/*"><br><br>

        <label for="brand">Brand:</label><br>
        <select name="brand" id="brand" required onchange="updateModels()">
            <option value="">Select Brand</option>
            <?php foreach (array_keys($brandsWithModels) as $brandOption): ?>
                <option value="<?php echo htmlspecialchars($brandOption); ?>"><?php echo htmlspecialchars($brandOption); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="model">Model:</label><br>
        <select name="model" id="model" required>
            <option value="">Select Model</option>
        </select><br><br>

        <label for="vehicleoverview">Vehicle Overview:</label><br>
        <textarea name="vehicleoverview" id="vehicleoverview" rows="4" required></textarea><br><br>

        <label for="priceperday">Price Per Day:</label><br>
        <input type="text" name="priceperday" id="priceperday" required><br><br>

        <label for="fueltype">Fuel Type:</label><br>
        <select name="fueltype" id="fueltype" required>
            <option value="">Select Fuel Type</option>
            <?php foreach ($fuelTypes as $fuel): ?>
                <option value="<?php echo htmlspecialchars($fuel); ?>"><?php echo htmlspecialchars($fuel); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="modelyear">Model Year:</label><br>
        <select name="modelyear" id="modelyear" required>
            <option value="">Select Model Year</option>
            <?php foreach ($modelYears as $year): ?>
                <option value="<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="nbrpersonne">Number of Persons:</label><br>
        <select name="nbrpersonne" id="nbrpersonne" required>
            <option value="">Select Number of Persons</option>
            <?php foreach ($personOptions as $person): ?>
                <option value="<?php echo htmlspecialchars($person); ?>"><?php echo htmlspecialchars($person); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="disponible">Available:</label><br>
        <select name="disponible" id="disponible" required>
            <option value="oui">Yes</option>
            <option value="non">No</option>
        </select><br><br>

        <input type="submit" name="submit" value="Add Car">
    </form>

    <script>
        // JavaScript to update the models based on the selected brand
        const brandsWithModels = <?php echo json_encode($brandsWithModels); ?>;

        function updateModels() {
            const brand = document.getElementById('brand').value;
            const modelSelect = document.getElementById('model');

            // Clear the current models
            modelSelect.innerHTML = '<option value="">Select Model</option>';

            if (brandsWithModels[brand]) {
                brandsWithModels[brand].forEach(function(model) {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            }
        }
    </script>
</body>
</html>
