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

// Save student
function save_student($conn) {    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ensure all necessary fields are present
        if (isset($_POST['student_id'], $_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['college'], $_POST['program'], $_POST['year'])) {
            $student_id = $_POST['student_id'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];
            $college = $_POST['college'];
            $program = $_POST['program'];
            $year = $_POST['year'];

            $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, middle_name, last_name, college, program, year) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $first_name, $middle_name, $last_name, $college, $program, $year]);

            echo json_encode(['message' => 'Student entry saved successfully!']);
        } else {
            echo json_encode(['message' => 'Missing required fields.'], JSON_FORCE_OBJECT);
        }
    } else {
        echo json_encode(['message' => 'Invalid request method.']);
    }
}

// Delete student
function delete_student($conn) {
    if (isset($_GET['id'])) {
        $student_id = $_GET['id']; // Get the student ID from the query string

        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Student deleted successfully.']);
        } else {
            echo json_encode(['message' => 'Student not found or already deleted.']);
        }
    } else {
        echo json_encode(['message' => 'Student ID is missing.']);
    }
}

// Edit student
function edit_student($conn) {
    if (isset($_POST['student_id'], $_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['college'], $_POST['program'], $_POST['year'])) {
        $student_id = $_POST['student_id'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $college = $_POST['college'];
        $program = $_POST['program'];
        $year = $_POST['year'];

        // SQL update logic...
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, college = ?, program = ?, year = ? WHERE student_id = ?");
        $stmt->execute([$first_name, $middle_name, $last_name, $college, $program, $year, $student_id]);

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Student information updated successfully.']);
        } else {
            echo json_encode(['message' => 'No changes were made or student not found.']);
        }
    } else {
        echo json_encode(['message' => 'Missing required fields for updating student.']);
    }
}


// Get college data
function get_college($conn) {
    $stmt = $conn->query("SELECT collid, collfullname FROM colleges");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colleges);
}

// Get program data based on the college name
function get_program($conn) {
    if (isset($_GET['progFullName'])) {
        $progFullName = $_GET['progFullName'];  // Get the college full name
        
        $stmt = $conn->prepare("SELECT progid, progfullname, progcollid FROM programs WHERE progcollid IN 
                                (SELECT collid FROM colleges WHERE collfullname = :progFullName)");
        $stmt->bindParam(':progFullName', $progFullName, PDO::PARAM_STR);
        $stmt->execute();
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($programs);
    } else {
        echo json_encode(['message' => 'Program Full Name is required.']);
    }
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit']) && $_POST['edit'] == true) {
        // Call the edit function if the edit flag is set
        edit_student($conn);
    } else {
        // Otherwise, it's a save request (new student entry)
        save_student($conn);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $type = $_GET['type'] ?? ''; // Safely get the 'type' parameter

    if ($type == 'colleges') {
        get_college($conn);
    }
    if ($type == 'programs') {
        get_program($conn);
    }
    if (isset($_GET['id'])) {
        // Handle delete request
        delete_student($conn);
    }
}
?>
