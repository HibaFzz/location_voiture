 
<!DOCTYPE html>
<html>
<head>
    <title>Cars</title>
</head>
<body>
    <h2>Car List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Matricule</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($cars as $car): ?>
        <tr>
            <td><?= $car['id'] ?></td>
            <td><?= $car['matricule'] ?></td>
            <td><img src="/uploads/<?= $car['image'] ?>" width="100"></td>
            <td>
                <a href="/public/index.php?action=editCar&id=<?= $car['id'] ?>">Edit</a>
                <a href="/public/index.php?action=deleteCar&id=<?= $car['id'] ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="/public/index.php?action=createCar">Create Car</a>
</body>
</html>
