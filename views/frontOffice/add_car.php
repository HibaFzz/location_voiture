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

if (isset($_POST['submit'])) {

    $matricule = trim($_POST['matricule']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']); 
    $vehicleoverview = trim($_POST['vehicleoverview']);
    $priceperday = trim($_POST['priceperday']);
    $fueltype = trim($_POST['fueltype']);
    $modelyear = trim($_POST['modelyear']);
    $nbrpersonne = trim($_POST['nbrpersonne']);
    $disponible = isset($_POST['disponible']) ? (int)$_POST['disponible'] : 0; 

    $target_file = ""; 


    $matriculePattern = '/^\d{3}TUN\d{4}$/'; 
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

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

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

        if ($_FILES["image"]["size"] > 500000) {
            $errors['image'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['image'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1 && empty($errors)) {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

            } else {
                $errors['image'] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {

        $target_file = $carsController->getCarImagePathByMatricule($matricule);
    }


    if (empty($errors)) {      

        $vehicleTitle = $brand . ' ' . $model; 
        $carsController->addCar($matricule, $target_file, $vehicleTitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible); 
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
            color: black;
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
            max-width: 1000px;
            display: flex;
            flex-direction: column; /* Change to column for vertical stacking */
            gap: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            width: 200px; /* Set a specific width */
            margin: 0 auto; /* Center the button */
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
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: black;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea,
        .form-group input[type="file"] {
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
            width: 100%;
        }
        .form-group span {
            color: #ff0000;
            font-size: 0.875em;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Add a New Car</h1>
    <form method="POST" enctype="multipart/form-data">

        <!-- Section 1: General Information -->
        <fieldset>
            <legend>General Information</legend>

            <div class="form-group">
                <label for="matricule">Matricule (e.g. 111TUN1111):</label>
                <input type="text" id="matricule" name="matricule" value="<?php echo $matricule ?? ''; ?>">
                <?php if (isset($errors['matricule'])): ?>
                    <span><?php echo $errors['matricule']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="brand">Brand:</label>
                <select id="brand" name="brand">
                    <option value="">-- Select a Brand --</option>
                    <?php foreach ($brandsWithModels as $brandOption => $models): ?>
                        <option value="<?php echo $brandOption; ?>" <?php echo (isset($brand) && $brand == $brandOption) ? 'selected' : ''; ?>>
                            <?php echo $brandOption; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['brand'])): ?>
                    <span><?php echo $errors['brand']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="model">Model:</label>
                <select id="model" name="model">
                    <option value="">-- Select a Model --</option>
                    <?php if (isset($brand) && isset($brandsWithModels[$brand])): ?>
                        <?php foreach ($brandsWithModels[$brand] as $modelOption): ?>
                            <option value="<?php echo $modelOption; ?>" <?php echo (isset($model) && $model == $modelOption) ? 'selected' : ''; ?>>
                                <?php echo $modelOption; ?>
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
                <textarea id="vehicleoverview" name="vehicleoverview"><?php echo $vehicleoverview ?? ''; ?></textarea>
            </div>

        </fieldset>

        <!-- Section 2: Vehicle Details -->
        <fieldset>
            <legend>Vehicle Details</legend>

            <div class="form-group">
                <label for="priceperday">Price Per Day:</label>
                <input type="text" id="priceperday" name="priceperday" value="<?php echo $priceperday ?? ''; ?>">
                <?php if (isset($errors['priceperday'])): ?>
                    <span><?php echo $errors['priceperday']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="fueltype">Fuel Type:</label>
                <select id="fueltype" name="fueltype">
                    <option value="">-- Select Fuel Type --</option>
                    <?php foreach ($fuelTypes as $fuelType): ?>
                        <option value="<?php echo $fuelType; ?>" <?php echo (isset($fueltype) && $fueltype == $fuelType) ? 'selected' : ''; ?>>
                            <?php echo $fuelType; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['fueltype'])): ?>
                    <span><?php echo $errors['fueltype']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="modelyear">Model Year:</label>
                <select id="modelyear" name="modelyear">
                    <option value="">-- Select Model Year --</option>
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
                <select id="nbrpersonne" name="nbrpersonne">
                    <option value="">-- Select Number of Persons --</option>
                    <?php foreach ($personOptions as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo (isset($nbrpersonne) && $nbrpersonne == $option) ? 'selected' : ''; ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['nbrpersonne'])): ?>
                    <span><?php echo $errors['nbrpersonne']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="disponible">Availability:</label>
                <select id="disponible" name="disponible">
                    <?php foreach ($disponibleOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo (isset($disponible) && $disponible == $value) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image">
                <?php if (isset($errors['image'])): ?>
                    <span><?php echo $errors['image']; ?></span>
                <?php endif; ?>
            </div>

        </fieldset>

        <div style="text-align: center;">
            <input type="submit" name="submit" value="Add Car">
        </div>
    </form>
    <?php include('footer.php'); ?>
</body>
</html>

<script>
const brandsWithModels = <?= json_encode($brandsWithModels) ?>;
const brandSelect = document.getElementById("brand");
const modelSelect = document.getElementById("model");

brandSelect.addEventListener("change", function() {
    const selectedBrand = this.value;
    modelSelect.innerHTML = '<option value="">-- Select a Model --</option>';

    if (selectedBrand in brandsWithModels) {
        brandsWithModels[selectedBrand].forEach(function(model) {
            const option = document.createElement("option");
            option.value = model;
            option.textContent = model;
            modelSelect.appendChild(option);
        });
    }
});
</script>
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
            color: black;
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
            max-width: 1000px;
            display: flex;
            flex-direction: column; /* Change to column for vertical stacking */
            gap: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            width: 200px; /* Set a specific width */
            margin: 0 auto; /* Center the button */
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
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: black;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea,
        .form-group input[type="file"] {
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 4px;
            width: 100%;
        }
        .form-group span {
            color: #ff0000;
            font-size: 0.875em;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Add a New Car</h1>
    <form method="POST" enctype="multipart/form-data">

        <!-- Section 1: General Information -->
        <fieldset>
            <legend>General Information</legend>

            <div class="form-group">
                <label for="matricule">Matricule (e.g. 111TUN1111):</label>
                <input type="text" id="matricule" name="matricule" value="<?php echo $matricule ?? ''; ?>">
                <?php if (isset($errors['matricule'])): ?>
                    <span><?php echo $errors['matricule']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="brand">Brand:</label>
                <select id="brand" name="brand">
                    <option value="">-- Select a Brand --</option>
                    <?php foreach ($brandsWithModels as $brandOption => $models): ?>
                        <option value="<?php echo $brandOption; ?>" <?php echo (isset($brand) && $brand == $brandOption) ? 'selected' : ''; ?>>
                            <?php echo $brandOption; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['brand'])): ?>
                    <span><?php echo $errors['brand']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="model">Model:</label>
                <select id="model" name="model">
                    <option value="">-- Select a Model --</option>
                    <?php if (isset($brand) && isset($brandsWithModels[$brand])): ?>
                        <?php foreach ($brandsWithModels[$brand] as $modelOption): ?>
                            <option value="<?php echo $modelOption; ?>" <?php echo (isset($model) && $model == $modelOption) ? 'selected' : ''; ?>>
                                <?php echo $modelOption; ?>
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
                <textarea id="vehicleoverview" name="vehicleoverview"><?php echo $vehicleoverview ?? ''; ?></textarea>
            </div>

        </fieldset>

        <!-- Section 2: Vehicle Details -->
        <fieldset>
            <legend>Vehicle Details</legend>

            <div class="form-group">
                <label for="priceperday">Price Per Day:</label>
                <input type="text" id="priceperday" name="priceperday" value="<?php echo $priceperday ?? ''; ?>">
                <?php if (isset($errors['priceperday'])): ?>
                    <span><?php echo $errors['priceperday']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="fueltype">Fuel Type:</label>
                <select id="fueltype" name="fueltype">
                    <option value="">-- Select Fuel Type --</option>
                    <?php foreach ($fuelTypes as $fuelType): ?>
                        <option value="<?php echo $fuelType; ?>" <?php echo (isset($fueltype) && $fueltype == $fuelType) ? 'selected' : ''; ?>>
                            <?php echo $fuelType; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['fueltype'])): ?>
                    <span><?php echo $errors['fueltype']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="modelyear">Model Year:</label>
                <select id="modelyear" name="modelyear">
                    <option value="">-- Select Model Year --</option>
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
                <select id="nbrpersonne" name="nbrpersonne">
                    <option value="">-- Select Number of Persons --</option>
                    <?php foreach ($personOptions as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo (isset($nbrpersonne) && $nbrpersonne == $option) ? 'selected' : ''; ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['nbrpersonne'])): ?>
                    <span><?php echo $errors['nbrpersonne']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="disponible">Availability:</label>
                <select id="disponible" name="disponible">
                    <?php foreach ($disponibleOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo (isset($disponible) && $disponible == $value) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image">
                <?php if (isset($errors['image'])): ?>
                    <span><?php echo $errors['image']; ?></span>
                <?php endif; ?>
            </div>

        </fieldset>

        <div style="text-align: center;">
            <input type="submit" name="submit" value="Add Car">
        </div>
    </form>
    <?php include('footer.php'); ?>
</body>
</html>

<script>
const brandsWithModels = <?= json_encode($brandsWithModels) ?>;
const brandSelect = document.getElementById("brand");
const modelSelect = document.getElementById("model");

brandSelect.addEventListener("change", function() {
    const selectedBrand = this.value;
    modelSelect.innerHTML = '<option value="">-- Select a Model --</option>';

    if (selectedBrand in brandsWithModels) {
        brandsWithModels[selectedBrand].forEach(function(model) {
            const option = document.createElement("option");
            option.value = model;
            option.textContent = model;
            modelSelect.appendChild(option);
        });
    }
});
</script>
