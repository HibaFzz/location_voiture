<?php
include '../../controllers/CarController.php'; // Ensure this path is correct

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
$fuelTypes = ['essence', 'diesel','electric'];

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
        echo "<p>" . $error . "</p>"; // Output error message directly
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
            background-color: #f7f9fc; /* Light gray background */
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff; /* Blue color for sub-title */
            font-size: 2em; /* Size of the title */
            margin-bottom: 20px; /* Space below the title */
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px; /* Maximum width for form */
        }

        .form-section {
            display: flex; /* Flexbox layout */
            justify-content: space-between; /* Space between the sections */
            margin: 20px 0; /* Margin for space between sections */
        }

        .section {
            flex: 1; /* Equal growth for sections */
            min-width: 300px; /* Minimum width for sections */
            max-width: 400px; /* Maximum width for sections */
            padding: 0 10px; /* Side padding */
        }

        .section-title {
            font-size: 1.5em;
            color: #007bff; /* Blue for section titles */
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff; /* Underline effect */
            padding-bottom: 5px; /* Space below title */
        }

        .form-group {
            display: flex; /* Flexbox for form groups */
            margin-bottom: 15px; /* Space between form groups */
        }

        .form-group label {
            flex-basis: 35%; /* Width for label */
            margin-right: 10px; /* Space between label and input */
            font-weight: bold; /* Bold labels */
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            flex-basis: 60%; /* Adjust width of input/select */
            padding: 8px; /* Padding for inputs */
            border: 1px solid #007bff; /* Bootstrap primary color */
            border-radius: 4px;
            transition: border-color 0.3s; /* Transition for border color on focus */
        }

        .form-group select:focus,
        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: #0056b3; /* Darker blue on focus */
            outline: none; /* Remove default outline */
        }

        input[type="submit"] {
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s; /* Transition effects */
            margin-top: 10px;
            width: 100%; /* Full width button */
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: translateY(-2px); /* Lift effect */
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
        }

        .return-link {
            text-align: center; /* Center the return link */
            margin-top: 20px; /* Space above the return link */
            font-size: 1.2em; /* Size of the return link */
        }

        .return-link a {
            color: #007bff; /* Blue color for link */
            text-decoration: none; /* Remove underline */
        }

        .return-link a:hover {
            text-decoration: underline; /* Underline on hover */
        }

        /* Responsive design for smaller screens */
        @media (max-width: 600px) {
            .form-section {
                flex-direction: column; /* Stack sections on small screens */
            }

            .form-group {
                flex-direction: column; /* Stack labels and inputs */
                align-items: flex-start; /* Align items to the start */
            }

            .form-group label {
                flex-basis: auto; /* Reset label width */
                margin-bottom: 5px; /* Space between label and input */
            }

            .form-group select,
            .form-group input[type="text"],
            .form-group textarea {
                flex-basis: 100%; /* Full width on small screens */
            }

            input[type="submit"] {
                width: auto; /* Reset button width */
            }
        }
    </style>
</head>
<body>
    <h2>Add A New Car</h2>

    <!-- Display errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to add a new car -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <div class="section">
                <div class="section-title">Car Information</div>
                <div class="form-group">
                    <label for="matricule">Matricule (Format: 111TUN1111):</label>
                    <input type="text" name="matricule" id="matricule" required>
                </div>

                <div class="form-group">
                    <label for="image">Image (Optional):</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="brand">Brand:</label>
                    <select name="brand" id="brand" required onchange="updateModels()">
                        <option value="">Select Brand</option>
                        <?php foreach (array_keys($brandsWithModels) as $brandOption): ?>
                            <option value="<?php echo $brandOption; ?>"><?php echo $brandOption; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="model">Model:</label>
                    <select name="model" id="model" required>
                        <option value="">Select Model</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="vehicleoverview">Vehicle Overview:</label>
                    <textarea name="vehicleoverview" id="vehicleoverview" rows="4" placeholder="Provide a brief overview of the vehicle..."></textarea>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Pricing & Availability</div>
                <div class="form-group">
                    <label for="priceperday">Price Per Day (in currency):</label>
                    <input type="text" name="priceperday" id="priceperday" required placeholder="Enter price...">
                </div>

                <div class="form-group">
                    <label for="fueltype">Fuel Type:</label>
                    <select name="fueltype" id="fueltype" required>
                        <option value="">Select Fuel Type</option>
                        <?php foreach ($fuelTypes as $fuel): ?>
                            <option value="<?php echo $fuel; ?>"><?php echo $fuel; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modelyear">Model Year:</label>
                    <select name="modelyear" id="modelyear" required>
                        <option value="">Select Model Year</option>
                        <?php foreach ($modelYears as $year): ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nbrpersonne">Number of Persons:</label>
                    <select name="nbrpersonne" id="nbrpersonne" required>
                        <option value="">Select Number of Persons</option>
                        <?php foreach ($personOptions as $person): ?>
                            <option value="<?php echo $person; ?>"><?php echo $person; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="disponible">Available:</label>
                    <select name="disponible" id="disponible" required>
                        <?php foreach ($disponibleOptions as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?= (isset($disponible) && $disponible == $value) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <input type="submit" name="submit" value="Add Car">
    </form>

    <!-- Return to list of cars link -->
    <div class="return-link">
        <a href="list_cars.php">Return to List Cars</a>
    </div>

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
