<?php
$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

// Create a global connection
try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Save college
function save_college($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['collfullname']) && isset($_POST['collshortname'])) {
            $collfullname = $_POST['collfullname'];
            $collshortname = $_POST['collshortname'];

            // Get the highest existing collid in the database
            $query = "SELECT MAX(collid) AS max_id FROM colleges";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextCollId = $result['max_id'] + 1;  // Increment the max id to get the next collid

            // Insert the new college into the database
            $insertQuery = "INSERT INTO colleges (collid, collfullname, collshortname) 
                            VALUES (:collid, :collfullname, :collshortname)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(':collid', $nextCollId);
            $insertStmt->bindParam(':collfullname', $collfullname);
            $insertStmt->bindParam(':collshortname', $collshortname);

            if ($insertStmt->execute()) {
                respond("success", "College added successfully.");
            } else {
                respond("error", "Failed to add college.");
            }
        } else {
            respond("error", "Invalid input data.");
        }
    }
}

// Delete college
function delete_college($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['collid'])) {
            $collid = $_POST['collid'];

            // First, delete any departments that are associated with this college
            $deleteDepartmentsQuery = "DELETE FROM departments WHERE deptcollid = :collid";
            $deleteDepartmentsStmt = $conn->prepare($deleteDepartmentsQuery);
            $deleteDepartmentsStmt->bindParam(':collid', $collid);
            $deleteDepartmentsStmt->execute();

            // Then, delete the college itself
            $deleteQuery = "DELETE FROM colleges WHERE collid = :collid";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':collid', $collid);

            if ($deleteStmt->execute()) {
                respond("success", "College and related departments deleted successfully.");
            } else {
                respond("error", "Failed to delete college.");
            }
        } else {
            respond("error", "College ID is required.");
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get PUT data
    $data = json_decode(file_get_contents('php://input'), true);
    error_log('Received data: ' . print_r($data, true));  // This will print the data to the PHP error log

    // Validate input
    $collID = $data['collid'] ?? null;
    $collFullName = $data['collfullname'] ?? null;
    $collShortName = $data['collshortname'] ?? null;


    if (!$collID || !$collFullName || !$collShortName || !is_numeric($collID)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid or missing data.']);
        exit;
    }

    try {
        // Update the college record
        $stmt = $conn->prepare("
                UPDATE colleges
                SET collfullname = :collFullName,
                    collshortname = :collShortName
                WHERE collid = :collID
            ");
        $stmt->bindParam(':collID', $collID, PDO::PARAM_INT);
        $stmt->bindParam(':collFullName', $collFullName, PDO::PARAM_STR);
        $stmt->bindParam(':collShortName', $collShortName, PDO::PARAM_STR);
        $stmt->execute();

        // Respond with success
        http_response_code(200); // OK
        echo json_encode(['message' => 'College updated successfully.']);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to update college.', 'error' => $e->getMessage()]);
    }
}





// Function to send response
function respond($status, $message)
{
    echo json_encode(['status' => $status, 'message' => $message]);
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['collid']) && isset($_POST['collfullname']) && isset($_POST['collshortname'])) {
        edit_college($conn); // Call edit function if data is for editing
    } else if (isset($_POST['collid'])) {
        delete_college($conn); // Call delete function if data is for deletion
    } else {
        save_college($conn); // Call save function if data is for saving a new college
    }
}
?>