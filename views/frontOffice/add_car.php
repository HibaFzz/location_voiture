<?php
include '../../controllers/CarController.php'; // Ensure this path is correct
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);

$carsController = new CarController();
$errors = []; // Initialize an array to hold error messages
$uploadOk = 1; // Variable to track if the upload should proceed
$disponibleOptions = [
    1 => "Available",
    0 => "Not Available"
];

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
$fuelTypes = ['essence', 'diesel', 'electric'];

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
    $disponible = isset($_POST['disponible']) ? (int)$_POST['disponible'] : 0; // Default to 0 if not set

    // Assume a default image path (this could be retrieved from your database)
    $target_file = ""; // Initialize the image path

    // Validate input fields
    $matriculePattern = '/^\d{3}TUN\d{4}$/'; // Pattern for matricule (111TUN1111 format)
    if (empty($matricule)) {
        $errors['matricule'] = "Matricule is required.";
    } elseif (!preg_match($matriculePattern, $matricule)) {
        $errors['matricule'] = "Matricule must be in the format 111TUN1111.";
    }

    if (empty($brand)) {
        $errors['brand'] = "Brand is required.";
    }

    if (empty($model)) {
        $errors['model'] = "Model is required.";
    }

    if (empty($priceperday) || !is_numeric($priceperday)) {
        $errors['priceperday'] = "Valid price per day is required.";
    }

    if (empty($fueltype)) {
        $errors['fueltype'] = "Fuel type is required.";
    }

    if (empty($modelyear)) {
        $errors['modelyear'] = "Model year is required.";
    }

    if (empty($nbrpersonne) || !is_numeric($nbrpersonne)) {
        $errors['nbrpersonne'] = "Number of persons must be a valid number.";
    }

    // Handle the image only if a new one is uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        // Only process the upload if a new file is provided
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate the image
        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $errors['image'] = "Image upload failed with error code: " . $_FILES["image"]["error"];
            $uploadOk = 0;
        } else {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                $errors['image'] = "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Other validations (size, format, etc.)
        if ($_FILES["image"]["size"] > 500000) {
            $errors['image'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['image'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
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
                $errors['image'] = "Sorry, there was an error uploading your file.";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Car</title>
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

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        fieldset {
            margin-bottom: 20px;
            border: 1px solid #007bff;
            padding: 15px;
            border-radius: 8px;
        }

        legend {
            font-weight: bold;
            font-size: 1.2em;
            color: #2c3e50;
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
        .form-group select,
        .form-group textarea,
        .form-group input[type="file"] {
            flex-basis: 60%;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .form-group select:focus,
        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group input[type="file"]:focus {
            border-color: #0056b3;
            outline: none;
        }

        .form-group span {
            color: #ff0000; /* Red color for error messages */
            font-size: 0.875em; /* Smaller size for error messages */
            margin-left: 5px; /* Space between input and error */
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error-summary {
            background-color: #ffe6e6;
            color: #d9534f;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #d9534f;
        }
    </style>
</head>
<body>
<div style="padding-top: 100px;"></div>
<h1>Add New Car</h1>



<form action="add_car.php" method="POST" enctype="multipart/form-data">

    <!-- Section 1: Basic Information -->
    <fieldset>
        <legend>Basic Information</legend>

        <div class="form-group">
            <label for="matricule">Matricule:</label>
            <input type="text" name="matricule" id="matricule" value="<?php echo isset($matricule) ? $matricule : ''; ?>">
            <?php if (isset($errors['matricule'])): ?>
                <span><?php echo $errors['matricule']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="brand">Brand:</label>
            <select name="brand" id="brand">
                <option value="">Select a brand</option>
                <?php foreach (array_keys($brandsWithModels) as $b): ?>
                    <option value="<?php echo $b; ?>" <?php echo (isset($brand) && $brand === $b) ? 'selected' : ''; ?>>
                        <?php echo $b; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['brand'])): ?>
                <span><?php echo $errors['brand']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="model">Model:</label>
            <select name="model" id="model">
                <option value="">Select a model</option>
                <?php if (!empty($brand)): ?>
                    <?php foreach ($brandsWithModels[$brand] as $m): ?>
                        <option value="<?php echo $m; ?>" <?php echo (isset($model) && $model === $m) ? 'selected' : ''; ?>>
                            <?php echo $m; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (isset($errors['model'])): ?>
                <span><?php echo $errors['model']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="vehicleoverview">Vehicle Overview:</label>
            <textarea name="vehicleoverview" id="vehicleoverview"><?php echo isset($vehicleoverview) ? $vehicleoverview : ''; ?></textarea>
            <?php if (isset($errors['vehicleoverview'])): ?>
                <span><?php echo $errors['vehicleoverview']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="priceperday">Price Per Day:</label>
            <input type="text" name="priceperday" id="priceperday" value="<?php echo isset($priceperday) ? $priceperday : ''; ?>">
            <?php if (isset($errors['priceperday'])): ?>
                <span><?php echo $errors['priceperday']; ?></span>
            <?php endif; ?>
        </div>
    </fieldset>

    <!-- Section 2: Additional Information -->
    <fieldset>
        <legend>Additional Information</legend>

        <div class="form-group">
            <label for="fueltype">Fuel Type:</label>
            <select name="fueltype" id="fueltype">
                <option value="">Select a fuel type</option>
                <?php foreach ($fuelTypes as $fuel): ?>
                    <option value="<?php echo $fuel; ?>" <?php echo (isset($fueltype) && $fueltype === $fuel) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($fuel); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['fueltype'])): ?>
                <span><?php echo $errors['fueltype']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="modelyear">Model Year:</label>
            <select name="modelyear" id="modelyear">
                <option value="">Select a model year</option>
                <?php foreach ($modelYears as $year): ?>
                    <option value="<?php echo $year; ?>" <?php echo (isset($modelyear) && $modelyear == $year) ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['modelyear'])): ?>
                <span><?php echo $errors['modelyear']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="nbrpersonne">Number of Persons:</label>
            <select name="nbrpersonne" id="nbrpersonne">
                <option value="">Select number of persons</option>
                <?php foreach ($personOptions as $num): ?>
                    <option value="<?php echo $num; ?>" <?php echo (isset($nbrpersonne) && $nbrpersonne == $num) ? 'selected' : ''; ?>>
                        <?php echo $num; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['nbrpersonne'])): ?>
                <span><?php echo $errors['nbrpersonne']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="disponible">Available:</label>
            <select name="disponible" id="disponible">
                <?php foreach ($disponibleOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($disponible) && $disponible == $value) ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" name="image" id="image">
            <?php if (isset($errors['image'])): ?>
                <span><?php echo $errors['image']; ?></span>
            <?php endif; ?>
        </div>

    </fieldset>

    <div class="form-row">
            <input type="submit" name="add_car" value="Add Car">
        </div>
</form>

</body>
</html>



    <script>
        // Populate models dynamically based on selected brand
        const brandsWithModels = <?= json_encode($brandsWithModels) ?>;
        const brandSelect = document.getElementById('brand');
        const modelSelect = document.getElementById('model');

        brandSelect.addEventListener('change', function() {
            const selectedBrand = this.value;
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            if (brandsWithModels[selectedBrand]) {
                brandsWithModels[selectedBrand].forEach(function(model) {
                    const option = document.createElement('option');
                    option.value = model;
                    option.text = model;
                    modelSelect.appendChild(option);
                });
            }
        });
    </script>

