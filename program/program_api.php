<?php
session_start();

$dsn = "mysql:host=localhost;dbname=usjr-jsp1b40;charset=utf8";
$username = "root";
$password = "1234";

try {
    // Connect to the database
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle different actions based on the query parameter 'action'
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        // Fetch colleges for the dropdown
        if ($action == 'fetch_colleges') {
            $collegesQuery = "SELECT collid, collfullname FROM colleges";
            $collegesStmt = $conn->query($collegesQuery);
            $colleges = $collegesStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($colleges);
        }

        // Fetch departments based on the selected college
        elseif ($action == 'fetch_departments' && isset($_GET['college_id'])) {
            $collegeId = $_GET['college_id'];

            $departmentsQuery = "SELECT deptid, deptfullname FROM departments WHERE deptcollid = :college_id";
            $stmt = $conn->prepare($departmentsQuery);
            $stmt->bindParam(':college_id', $collegeId, PDO::PARAM_INT);
            $stmt->execute();

            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($departments);
        }

        // Insert a new program into the database
        elseif ($action == 'insert_program' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['progfullname'], $_POST['progshortname'], $_POST['progcollid'], $_POST['progcolldeptid'])) {
                $progfullname = $_POST['progfullname'];
                $progshortname = $_POST['progshortname'];
                $progcollid = $_POST['progcollid'];
                $progcolldeptid = $_POST['progcolldeptid'];

                $query = "SELECT MAX(progid) AS max_progid FROM programs";
                $stmt = $conn->query($query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $newProgid = $result['max_progid'] + 1;

                $insertQuery = "INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid)
                                VALUES (:progid, :progfullname, :progshortname, :progcollid, :progcolldeptid)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bindParam(':progid', $newProgid);
                $stmt->bindParam(':progfullname', $progfullname);
                $stmt->bindParam(':progshortname', $progshortname);
                $stmt->bindParam(':progcollid', $progcollid);
                $stmt->bindParam(':progcolldeptid', $progcolldeptid);

                if ($stmt->execute()) {
                    echo "<script>alert('Program added successfully!');</script>";
                } else {
                    echo "<script>alert('Error adding program.');</script>";
                }

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields. Please ensure all fields are provided.']);
            }
        }

        // Delete a program from the database
        elseif ($action == 'delete_program' && isset($_GET['progid'])) {
            $progid = $_GET['progid'];

            $deleteQuery = "DELETE FROM programs WHERE progid = :progid";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bindParam(':progid', $progid, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Program deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error deleting program.']);
            }
        }

        // Edit an existing program
        elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            if (isset($_POST['progid'], $_POST['progfullname'], $_POST['progshortname'], $_POST['progcollid'], $_POST['progcolldeptid'])) {
                $progid = $_POST['progid'];
                $progfullname = $_POST['progfullname'];
                $progshortname = $_POST['progshortname'];
                $progcollid = $_POST['progcollid'];
                $progcolldeptid = $_POST['progcolldeptid'];

                // Update program query
                $updateQuery = "UPDATE programs 
                                SET progfullname = :progfullname, progshortname = :progshortname, progcollid = :progcollid, progcolldeptid = :progcolldeptid
                                WHERE progid = :progid";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bindParam(':progid', $progid);
                $stmt->bindParam(':progfullname', $progfullname);
                $stmt->bindParam(':progshortname', $progshortname);
                $stmt->bindParam(':progcollid', $progcollid);
                $stmt->bindParam(':progcolldeptid', $progcolldeptid);

                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Program updated successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error updating program.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields. Please ensure all fields are provided.']);
            }
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'No action specified.']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]);
}
?>