<?php
include '../../controllers/CarController.php';

$carsController = new CarController();

if (isset($_GET['id'])) {
    $car = $carsController->getCar($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $matricule = trim($_POST['matricule']);
    $vehicletitle = trim($_POST['vehicletitle']);
    $brand = trim($_POST['brand']);
    $vehicleoverview = trim($_POST['vehicleoverview']);
    $priceperday = trim($_POST['priceperday']);
    $fueltype = trim($_POST['fueltype']);
    $modelyear = trim($_POST['modelyear']);
    $nbrpersonne = trim($_POST['nbrpersonne']);
    $disponible = trim($_POST['disponible']); // Capture the disponible value
    $image = $_FILES['image']['name'];

    // Simple validation
    $errors = [];
    if (empty($matricule)) {
        $errors[] = "Matricule is required.";
    }
    if (empty($brand)) {
        $errors[] = "Brand is required.";
    }
    // Add other validations for new fields here...

    // If there are no errors, proceed to update the car
    if (empty($errors)) {
        if (!empty($image)) {
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
            $carsController->updateCar($id, $matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible); // Pass disponible
        } else {
            $carsController->updateCar($id, $matricule, $car['image'], $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible); // Pass disponible
        }
        header('Location: list_cars.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Car</title>
</head>
<body>
    <h1>Update Car</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $car['id']; ?>">
        
        <label for="matricule">Matricule:</label><br>
        <input type="text" name="matricule" id="matricule" value="<?= htmlspecialchars($car['matricule']); ?>" required><br><br>
        
        <label for="image">Image:</label><br>
        <input type="file" name="image" id="image" accept="image/*"><br><br>
        
        <label for="vehicletitle">Vehicle Title:</label><br>
        <input type="text" name="vehicletitle" id="vehicletitle" value="<?= htmlspecialchars($car['vehicletitle']); ?>" required><br><br>
        
        <label for="brand">Brand:</label><br>
        <input type="text" name="brand" id="brand" value="<?= htmlspecialchars($car['brand']); ?>" required><br><br>
        
        <label for="vehicleoverview">Vehicle Overview:</label><br>
        <textarea name="vehicleoverview" id="vehicleoverview" required><?= htmlspecialchars($car['vehicleoverview']); ?></textarea><br><br>
        
        <label for="priceperday">Price Per Day:</label><br>
        <input type="text" name="priceperday" id="priceperday" value="<?= htmlspecialchars($car['priceperday']); ?>" required><br><br>
        
        <label for="fueltype">Fuel Type:</label><br>
        <input type="text" name="fueltype" id="fueltype" value="<?= htmlspecialchars($car['fueltype']); ?>" required><br><br>
        
        <label for="modelyear">Model Year:</label><br>
        <input type="text" name="modelyear" id="modelyear" value="<?= htmlspecialchars($car['modelyear']); ?>" required><br><br>
        
        <label for="nbrpersonne">Number of Persons:</label><br>
        <input type="number" name="nbrpersonne" id="nbrpersonne" value="<?= htmlspecialchars($car['nbrpersonne']); ?>" required><br><br>
        
        <label for="disponible">Available:</label><br>
        <select name="disponible" id="disponible" required>
            <option value="oui" <?= ($car['disponible'] === 'oui') ? 'selected' : ''; ?>>Yes</option>
            <option value="non" <?= ($car['disponible'] === 'non') ? 'selected' : ''; ?>>No</option>
        </select><br><br>

        <input type="submit" value="Update Car">
    </form>
    <br>
    <a href="list_cars.php">Back to Car List</a>
</body>
</html>
