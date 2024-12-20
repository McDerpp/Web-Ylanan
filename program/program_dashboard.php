<?php
session_start();

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
                p.progid, 
                p.progfullname, 
                p.progshortname, 
                c.collfullname AS college_name, 
                d.deptfullname AS department_name 
            FROM programs p
            JOIN colleges c ON p.progcollid = c.collid
            JOIN departments d ON p.progcolldeptid = d.deptid";
    $stmt = $conn->query($sql);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Apply overflow to the container to handle large tables */
        .container {
            width: 80%;
            max-height: 600px; /* You can adjust the height */
            overflow-y: auto; /* Enables vertical scrolling if content exceeds max-height */
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        /* Style for the table inside the container */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        /* Scrollbar styling (optional) */
        .container::-webkit-scrollbar {
            width: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background-color: darkgray;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Programs Dashboard</h2>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
    <button onclick="window.location.href='program_entry.php'" class="btn-new-entry">New Program Entry</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Short Name</th>
                <th>College</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($programs as $program): ?>
                <tr>
                    <td><?php echo htmlspecialchars($program['progid']); ?></td>
                    <td><?php echo htmlspecialchars($program['progfullname']); ?></td>
                    <td><?php echo htmlspecialchars($program['progshortname']); ?></td>
                    <td><?php echo htmlspecialchars($program['college_name']); ?></td>
                    <td><?php echo htmlspecialchars($program['department_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
