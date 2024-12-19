<?php
require_once 'db.php'; // Include your database connection file

header('Content-Type: application/json');

$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['type'])) {
        $type = $_GET['type'];

        if ($type == 'colleges') {
            // Get all colleges
            $stmt = $conn->query("SELECT collid, collfullname FROM colleges");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($colleges);

        } elseif ($type == 'programs') {
            // Check if progFullName is set in the GET request
            if (isset($_GET['progFullName'])) {
                $progFullName = $_GET['progFullName'];  // Get the college full name

                // Query for programs in the specific college by its full name
                $stmt = $conn->prepare("SELECT progid, progfullname, progcollid FROM programs WHERE progcollid IN (SELECT collid FROM colleges WHERE collfullname = :progFullName)");
                $stmt->bindParam(':progFullName', $progFullName, PDO::PARAM_STR);
                $stmt->execute();
                $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($programs);
            } else {
                // If progFullName is not provided, get all programs
                $stmt = $conn->query("SELECT progid, progfullname, progcollid FROM programs");
                $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($programs);
            }
        }
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
