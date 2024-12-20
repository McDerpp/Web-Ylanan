<?php
header('Content-Type: application/json');

// Database connection settings
$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Database connection failed.', 'error' => $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $collegeId = $_GET['college'] ?? null;

    if (!$collegeId || !is_numeric($collegeId)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid or missing college ID.']);
        exit;
    }

    try {
        // Query the departments for the given college ID
        $stmt = $conn->prepare("
            SELECT deptid, deptfullname, deptshortname 
            FROM departments 
            WHERE deptcollid = :collegeId
        ");
        $stmt->bindParam(':collegeId', $collegeId, PDO::PARAM_INT);
        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$departments) {
            http_response_code(404); // Not Found
            echo json_encode(['message' => 'No departments found for the given college ID.']);
            exit;
        }

        // Return the results as JSON
        http_response_code(200); // OK
        echo json_encode($departments);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Query failed.', 'error' => $e->getMessage()]);
    }


}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $deptFullName = $data['deptfullname'] ?? null;
    $deptShortName = $data['deptshortname'] ?? null;
    $deptCollId = $data['deptcollid'] ?? null;
    $deptID = $data['deptID'] ?? null;

    if (!$deptFullName || !$deptShortName || !$deptCollId || !$deptID || !is_numeric($deptCollId)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid or missing data.']);
        exit;
    }

    try {
        // Insert the new department with the provided deptid
        $stmt = $conn->prepare("
            INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid)
            VALUES (:deptID, :deptFullName, :deptShortName, :deptCollId)
        ");
        $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);  // deptid provided manually
        $stmt->bindParam(':deptFullName', $deptFullName, PDO::PARAM_STR);
        $stmt->bindParam(':deptShortName', $deptShortName, PDO::PARAM_STR);
        $stmt->bindParam(':deptCollId', $deptCollId, PDO::PARAM_INT);

        $stmt->execute();

        // Respond with success
        http_response_code(201); // Created
        echo json_encode(['message' => 'Department added successfully.']);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to add department.', 'error' => $e->getMessage()]);
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $deptID = $data['deptid'] ?? null;
    $deptFullName = $data['deptfullname'] ?? null;
    $deptShortName = $data['deptshortname'] ?? null;
    $deptCollId = $data['deptcollid'] ?? null;


    if (!$deptID || !$deptFullName || !$deptShortName || !$deptCollId || !is_numeric($deptID) || !is_numeric($deptCollId)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid or missing data.']);
        exit;
    }

    try {
        // Update the department record
        $stmt = $conn->prepare("
            UPDATE departments
            SET deptfullname = :deptFullName,
                deptshortname = :deptShortName,
                deptcollid = :deptCollId
            WHERE deptid = :deptID
        ");
        $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
        $stmt->bindParam(':deptFullName', $deptFullName, PDO::PARAM_STR);
        $stmt->bindParam(':deptShortName', $deptShortName, PDO::PARAM_STR);
        $stmt->bindParam(':deptCollId', $deptCollId, PDO::PARAM_INT);
        $stmt->execute();

        // Respond with success
        http_response_code(200); // OK
        echo json_encode(['message' => 'Department updated successfully.']);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to update department.', 'error' => $e->getMessage()]);
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $deptID = $_GET['deptid'] ?? null;

    if (!$deptID || !is_numeric($deptID)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid or missing department ID.']);
        exit;
    }

    try {
        // Delete the department record
        $stmt = $conn->prepare("DELETE FROM departments WHERE deptid = :deptID");
        $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'Department deleted successfully.']);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['message' => 'Department not found.']);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to delete department.', 'error' => $e->getMessage()]);
    }
}







?>