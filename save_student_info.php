<?php
$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

$message = '';

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $college = $_POST['college'];
    $program = $_POST['program'];
    $year = $_POST['year'];

    $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, middle_name, last_name, college, program, year) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $first_name, $middle_name, $last_name, $college, $program, $year]);

    // Return a response (JSON)
    echo json_encode(['message' => 'Student entry saved successfully!']);
} else {
    echo json_encode(['message' => 'Invalid request method.']);
}

$conn = null;
?>
