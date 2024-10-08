 
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="/public/index.php?action=register" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <label>Date of Birth:</label>
        <input type="date" name="date_of_birth" required>
        <br>
        <label>CIN:</label>
        <input type="text" name="cin" required>
        <br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
