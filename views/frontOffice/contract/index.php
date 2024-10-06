 
<!DOCTYPE html>
<html>
<head>
    <title>Contracts</title>
</head>
<body>
    <h2>Contract List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Car ID</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($contracts as $contract): ?>
        <tr>
            <td><?= $contract['id'] ?></td>
            <td><?= $contract['user_id'] ?></td>
            <td><?= $contract['car_id'] ?></td>
            <td><?= $contract['start_date'] ?></td>
            <td><?= $contract['end_date'] ?></td>
            <td><?= $contract['status'] ?></td>
            <td>
                <a href="/public/index.php?action=editContract&id=<?= $contract['id'] ?>">Edit</a>
                <a href="/public/index.php?action=deleteContract&id=<?= $contract['id'] ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="/public/index.php?action=createContract">Create Contract</a>
</body>
</html>
