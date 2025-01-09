<?php

include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT 
                collid, 
                collfullname, 
                collshortname 
            FROM colleges";
    $stmt = $conn->query($sql);
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang"en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="../css/dashboards.css">




    <style>

    </style>
</head>

<body>
    <br></br>

    <div class="dashboard-grid">
        <a href="student/student_dashboard.php" class="dashboard-card">
            <img src="Media/cover.jpg" alt="Students" class="card-image">
            <div class="card-content">
                <h3>Student Dashboard</h3>
                <p>Manage student records</p>
            </div>
        </a>
        <a href="college/college_dashboard.php" class="dashboard-card">
            <img src="Media/college.jpg" alt="College" class="card-image">
            <div class="card-content">
                <h3>College Dashboard</h3>
                <p>Oversee college departments</p>
            </div>
        </a>
        <a href="department/department_dashboard.php" class="dashboard-card">
            <img src="Media/department.jpg" alt="Department" class="card-image">
            <div class="card-content">
                <h3>Department Dashboard</h3>
                <p>Coordinate department</p>
            </div>
        </a>
        <a href="program/program_dashboard.php" class="dashboard-card">
            <img src="Media/program.jpeg" alt="Program" class="card-image">
            <div class="card-content">
                <h3>Program Dashboard</h3>
                <p>Manage academic programs, curricula, and course offerings</p>
            </div>
        </a>
    </div>
    <footer class="footer">
        <p>&copy; 2024 University Management System. All rights reserved.</p>
    </footer>
</body>

</html>