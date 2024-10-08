<?php
include '../../controllers/CarController.php';

$carsController = new CarController();
$errors = [];
$uploadOk = 1;
$disponibleOptions = [
    1 => "Available",
    0 => "Not Available"
];

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

$fuelTypes = ['essence', 'diesel', 'electric'];
$currentYear = date("Y");
$modelYears = range(2000, $currentYear);
$personOptions = range(1, 10);
$target_file = "";

// Check if 'id' is set to fetch existing car data for updating
if (isset($_GET['id'])) {
    $carId = (int)$_GET['id'];
    $car = $carsController->getCar($carId);

    if ($car) {
        $matricule = $car['matricule'];
        $brand = $car['brand'];
        $vehicletitle = $car['vehicletitle'];
        $model = trim(str_replace($brand . ' ', '', $vehicletitle));
        $vehicleoverview = $car['vehicleoverview'];
        $priceperday = $car['priceperday'];
        $fueltype = $car['fueltype'];
        $modelyear = $car['modelyear'];
        $nbrpersonne = $car['nbrpersonne'];
        $disponible = $car['disponible'];
        $target_file = $car['image'];
    } else {
        $errors[] = "Car not found.";
    }
}

// Handle form submission for updating
if (isset($_POST['update_car']) && isset($carId)) {
    $matricule = trim($_POST['matricule']);
    $brandInput = trim($_POST['brand']);
    $modelInput = trim($_POST['model']);
    $vehicleoverview = trim($_POST['vehicleoverview']);
    $priceperday = trim($_POST['priceperday']);
    $fueltype = trim($_POST['fueltype']);
    $modelyear = trim($_POST['modelyear']);
    $nbrpersonne = trim($_POST['nbrpersonne']);
    $disponible = isset($_POST['disponible']) ? (int)$_POST['disponible'] : 0;

    if (!array_key_exists($brandInput, $brandsWithModels)) {
        $errors[] = "Brand not found.";
    } elseif (!in_array($modelInput, $brandsWithModels[$brandInput])) {
        $errors[] = "Model not found for the selected brand.";
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["image"]["size"] > 500000) {
            $errors[] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk && empty($errors)) {
            if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
                $errors[] = "Failed to create directory.";
            } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors[] = "Error uploading file.";
            }
        }
    }

    if (empty($errors)) {
        $vehicleTitle = $brandInput . ' ' . $modelInput;

        if (!$carsController->updateCar($carId, $matricule, $target_file, $vehicleTitle, $brandInput, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible)) {
            $errors[] = "Failed to update car. Please try again.";
        } else {
            header('Location: list_cars.php');
            exit();
        }
    }
}

if (!empty($errors)) {
    echo '<div class="error-messages">';
    foreach ($errors as $error) {
        echo "<p class='error-message'>" . htmlspecialchars($error) . "</p>";
    }
    echo '</div>';
}
?>

<html>
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Car</title>
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
            margin-top: 80px; /* Increased margin-top to add more space from the header */
            margin-bottom: 40px; 
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
        .form-group select,
        .form-group textarea {
            flex-basis: 60%;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .form-group select:focus,
        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: #0056b3;
            outline: none;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 10px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
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
                flex-basis: auto;
                margin-bottom: 5px;
            }

            .form-group select,
            .form-group input[type="text"],
            .form-group textarea {
                flex-basis: 100%;
            }

            input[type="submit"] {
                width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Include header at the top of the body -->
    <?php include('header.php'); ?>

    <!-- Wrap content below header in a container -->
    <div style="padding-top: 100px;"> <!-- Added padding-top to increase space from header -->
        <!-- Page Title -->
        <h1>Update Car</h1>

        <!-- Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <div class="section">
                    <div class="section-title">Car Information</div>
                    <div class="form-group">
                        <label for="matricule">Matricule (Format: 111TUN1111):</label>
                        <input type="text" name="matricule" id="matricule" required value="<?php echo $matricule ?? ''; ?>">
                    </div>

                    <div class="form-group" style="flex-direction: column;">
                        <label for="image">Image:</label>
                        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)" style="margin-bottom: 10px;">
                        <?php if ($target_file): ?>
                            <img id="imagePreview" src="<?php echo $target_file; ?>" alt="Current Image" style="max-width: 200px; margin-top: 10px;">
                        <?php else: ?>
                            <img id="imagePreview" style="display: none; max-width: 200px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                    <label for="brand">Brand:</label>
                        <select name="brand" id="brand" required>
                            <?php foreach ($brandsWithModels as $brandOption => $models): ?>
                                <option value="<?php echo $brandOption; ?>" <?php echo ($brand === $brandOption) ? 'selected' : ''; ?>><?php echo $brandOption; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="model">Model:</label>
                        <select name="model" id="model" required>
                            <?php foreach ($brandsWithModels[$brand] as $modelOption): ?>
                                <option value="<?php echo $modelOption; ?>" <?php echo ($model === $modelOption) ? 'selected' : ''; ?>><?php echo $modelOption; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Pricing And Availability</div>
                    <div class="form-group">
                        <label for="vehicleoverview">Overview:</label>
                        <textarea name="vehicleoverview" id="vehicleoverview" rows="4" required><?php echo $vehicleoverview ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="priceperday">Price per Day ($):</label>
                        <input type="text" name="priceperday" id="priceperday" required value="<?php echo $priceperday ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="fueltype">Fuel Type:</label>
                        <select name="fueltype" id="fueltype" required>
                            <?php foreach ($fuelTypes as $fuel): ?>
                                <option value="<?php echo $fuel; ?>" <?php echo (isset($fueltype) && $fueltype === $fuel) ? 'selected' : ''; ?>><?php echo ucfirst($fuel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modelyear">Model Year:</label>
                        <select name="modelyear" id="modelyear" required>
                            <?php foreach ($modelYears as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo (isset($modelyear) && $modelyear == $year) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nbrpersonne">Number of Seats:</label>
                        <select name="nbrpersonne" id="nbrpersonne" required>
                            <?php foreach ($personOptions as $option): ?>
                                <option value="<?php echo $option; ?>" <?php echo (isset($nbrpersonne) && $nbrpersonne == $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="disponible">Availability:</label>
                        <select name="disponible" id="disponible" required>
                            <?php foreach ($disponibleOptions as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($disponible) && $disponible === $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <input type="submit" name="update_car" value="Update Car">
        </form>

        <!-- Return Link -->
        <div class="return-link">
            <a href="list_cars.php">Back to Cars</a>
        </div>
    </div> <!-- End of container -->
</body>


    <script>
        function previewImage(event) {
            const output = document.getElementById('imagePreview');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.style.display = 'block';
        }
    </script>
    
</body>
<?php include('footer.php'); ?>
<script>
    const brandsWithModels = <?php echo json_encode($brandsWithModels); ?>;
    const brandSelect = document.querySelector('select[name="brand"]');
    const modelSelect = document.querySelector('select[name="model"]');
    
    // Preset selected model (if available)
    const selectedModel = "<?php echo $model ?? ''; ?>"; 

    function updateModelOptions() {
        const selectedBrand = brandSelect.value;
        modelSelect.innerHTML = ''; // Clear previous options

        if (brandsWithModels[selectedBrand]) {
            brandsWithModels[selectedBrand].forEach(function(model) {
                const option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                
                // Check if the current model matches the previously selected model
                if (model === selectedModel) {
                    option.selected = true;
                }

                modelSelect.appendChild(option);
            });
        }
    }

    // Add event listener to update models when brand is changed
    brandSelect.addEventListener('change', updateModelOptions);

    // Initialize the models on page load
    updateModelOptions();
</script>

</html>
