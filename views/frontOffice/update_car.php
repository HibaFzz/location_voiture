<?php
include '../../controllers/CarController.php';
require_once '../../controllers/AuthController.php';
AuthController::checkMultipleRoles(['client','agent']);
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

    if (empty($matricule)) {
        $errors['matricule'] = "Matricule is required.";
    } elseif (!preg_match('/^[0-9]{3}TUN[0-9]{4}$/', $matricule)) {
        $errors['matricule'] = "Matricule must be in the format 111TUN1111.";
    }

    if (!array_key_exists($brandInput, $brandsWithModels)) {
        $errors['brand'] = "Brand not found.";
    } elseif (!in_array($modelInput, $brandsWithModels[$brandInput])) {
        $errors['model'] = "Model not found for the selected brand.";
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

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["image"]["size"] > 500000) {
            $errors['image'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['image'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk && empty($errors)) {
            if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
                $errors['image'] = "Failed to create directory.";
            } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors['image'] = "Error uploading file.";
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

?>

<!DOCTYPE html>
<html lang="en">
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
            margin-top: 80px;
            margin-bottom: 40px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            max-width: 900px;
        }

        /* Flex container for two-column layout */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 280px;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: 100%;
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
            margin: 5px 0;
            text-align: left;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }

            .form-group {
                min-width: 100%;
            }

            input[type="submit"] {
                width: auto;
            }
        }
    </style>
</head>
<body>

    <?php include('header.php'); ?>

    <div style="padding-top: 100px;">
        <h1>Update Car</h1>

        <form action="" method="POST" enctype="multipart/form-data">
        <!-- Matricule and Image Section -->
        <div class="form-row">
            <div class="form-group">
                <label for="matricule">Matricule (Format: 111TUN1111):</label>
                <input type="text" name="matricule" id="matricule" value="<?php echo $matricule ?? ''; ?>">
                <?php if (isset($errors['matricule'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['matricule']; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
                <?php if (isset($errors['image'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['image']; ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($target_file)): ?>
                    <img id="imagePreview" src="<?php echo $target_file; ?>" alt="Car Image" style="max-width: 200px;">
                <?php endif; ?>
            </div>
        </div>

        <!-- Brand and Model Section -->
        <div class="form-row">
            <div class="form-group">
                <label for="brand">Brand:</label>
                <select name="brand" id="brand">
                    <?php foreach ($brandsWithModels as $brandOption => $models): ?>
                        <option value="<?php echo $brandOption; ?>" <?php echo ($brand === $brandOption) ? 'selected' : ''; ?>><?php echo $brandOption; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['brand'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['brand']; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="model">Model:</label>
                <select name="model" id="model">
                    <?php if (isset($brandsWithModels[$brand])): ?>
                        <?php foreach ($brandsWithModels[$brand] as $modelOption): ?>
                            <option value="<?php echo $modelOption; ?>" <?php echo (isset($model) && $model === $modelOption) ? 'selected' : ''; ?>><?php echo $modelOption; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (isset($errors['model'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['model']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Overview and Price Section -->
        <div class="form-row">
            <div class="form-group">
                <label for="vehicleoverview">Overview:</label>
                <textarea name="vehicleoverview" id="vehicleoverview"><?php echo $vehicleoverview ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="priceperday">Price per Day:</label>
                <input type="text" name="priceperday" id="priceperday" value="<?php echo $priceperday ?? ''; ?>">
                <?php if (isset($errors['priceperday'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['priceperday']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fuel Type and Model Year Section -->
        <div class="form-row">
            <div class="form-group">
                <label for="fueltype">Fuel Type:</label>
                <select name="fueltype" id="fueltype">
                    <option value="">Select Fuel Type</option>
                    <?php foreach ($fuelTypes as $fuel): ?>
                        <option value="<?php echo $fuel; ?>" <?php echo ($fueltype === $fuel) ? 'selected' : ''; ?>><?php echo ucfirst($fuel); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['fueltype'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['fueltype']; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="modelyear">Model Year:</label>
                <select name="modelyear" id="modelyear">
                    <option value="">Select Model Year</option>
                    <?php foreach ($modelYears as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo ($modelyear == $year) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['modelyear'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['modelyear']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Number of Persons and Availability Section -->
        <div class="form-row">
            <div class="form-group">
                <label for="nbrpersonne">Number of Persons:</label>
                <select name="nbrpersonne" id="nbrpersonne">
                    <option value="">Select Number of Persons</option>
                    <?php foreach ($personOptions as $person): ?>
                        <option value="<?php echo $person; ?>" <?php echo ($nbrpersonne == $person) ? 'selected' : ''; ?>><?php echo $person; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['nbrpersonne'])): ?>
                    <div class="error-message">
                        <p><?php echo $errors['nbrpersonne']; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="disponible">Available:</label>
                <select name="disponible" id="disponible">
                    <?php foreach ($disponibleOptions as $key => $option): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($disponible == $key) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-row">
            <input type="submit" name="update_car" value="Update Car">
        </div>
    </form>
    </div>
    <div style="text-align: center;"> <!-- Center the back button -->
            <a href="list_cars.php" class="back-button">Back to Car List</a>
        </div>



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
    
    const selectedModel = "<?php echo $model ?? ''; ?>"; 

    function updateModelOptions() {
        const selectedBrand = brandSelect.value;
        modelSelect.innerHTML = ''; 

        if (brandsWithModels[selectedBrand]) {
            brandsWithModels[selectedBrand].forEach(function(model) {
                const option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                
                if (model === selectedModel) {
                    option.selected = true;
                }

                modelSelect.appendChild(option);
            });
        }
    }

    brandSelect.addEventListener('change', updateModelOptions);


    updateModelOptions();
</script>

</html>
