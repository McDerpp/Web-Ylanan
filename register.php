<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "usjr-jsp1b40";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = htmlspecialchars(trim($data['username']));
    $password = htmlspecialchars(trim($data['password']));
    $verifyPassword = htmlspecialchars(trim($data['verify_password']));

    if ($password !== $verifyPassword) {
        $message = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = "Username already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                $message = "User registered successfully! <a href='login.php'>Login here</a>";
            } else {
                $message = "Error: " . $stmt->errorInfo()[2];
            }
        }
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <div id="message"><?php echo $message; ?></div>
        <form id="registerForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="verify_password">Verify Password:</label>
            <input type="password" id="verify_password" name="verify_password" required>

            <div class="buttons">
                <input type="submit" value="Register" class="btn-register">
                <input type="button" value="Login" class="btn-login" onclick="window.location.href='login.php'">
            </div>
        </form>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.text();
            document.getElementById('message').innerHTML = result;
        });
    </script>
</body>

</html>