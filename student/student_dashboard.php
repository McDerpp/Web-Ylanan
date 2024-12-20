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
                student_id, 
                last_name, 
                first_name, 
                middle_name, 
                college, 
                program, 
                year 
            FROM students";
    $stmt = $conn->query($sql);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> <!-- Axios CDN -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            padding: 20px;
            z-index: 1000;
            width: 400px;
        }
        .modal.active {
            display: block;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .modal-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Students Dashboard</h2>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
    <button onclick="window.location.href='student_entry.php'" class="btn-new-entry">New Student Entry</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>College</th>
                <th>Program</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr id="student-<?php echo $student['student_id']; ?>">
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['college']); ?></td>
                    <td><?php echo htmlspecialchars($student['program']); ?></td>
                    <td><?php echo htmlspecialchars($student['year']); ?></td>
                    <td>
                        <button class="btn-edit" data-id="<?php echo $student['student_id']; ?>" 
                                data-lastname="<?php echo htmlspecialchars($student['last_name']); ?>" 
                                data-firstname="<?php echo htmlspecialchars($student['first_name']); ?>" 
                                data-middlename="<?php echo htmlspecialchars($student['middle_name']); ?>" 
                                data-college="<?php echo htmlspecialchars($student['college']); ?>" 
                                data-program="<?php echo htmlspecialchars($student['program']); ?>" 
                                data-year="<?php echo htmlspecialchars($student['year']); ?>">Edit</button>
                        <button class="btn-delete" data-id="<?php echo $student['student_id']; ?>" onclick="deleteStudent(this)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="modal-overlay"></div>
<div class="modal" id="edit-modal">
    <h3>Edit Student</h3>
    <form id="edit-form">
        <input type="hidden" id="edit-id" name="student_id">
        <div>
            <label for="edit-lastname">Last Name:</label>
            <input type="text" id="edit-lastname" name="last_name" required>
        </div>
        <div>
            <label for="edit-firstname">First Name:</label>
            <input type="text" id="edit-firstname" name="first_name" required>
        </div>
        <div>
            <label for="edit-middlename">Middle Name:</label>
            <input type="text" id="edit-middlename" name="middle_name" required>
        </div>
        <div>
            <label for="edit-college">College:</label>
            <input type="text" id="edit-college" name="college" required>
        </div>
        <div>
            <label for="edit-program">Program:</label>
            <input type="text" id="edit-program" name="program" required>
        </div>
        <div>
            <label for="edit-year">Year:</label>
            <input type="number" id="edit-year" name="year" required>
        </div>
        <button type="submit">Save</button>
        <button type="button" onclick="closeModal()">Cancel</button>
    </form>
</div>

<script>
    const modal = document.getElementById('edit-modal');
    const overlay = document.getElementById('modal-overlay');

    function closeModal() {
        modal.classList.remove('active');
        overlay.classList.remove('active');
    }

    function deleteStudent(button) {
        const studentId = button.dataset.id;

        if (confirm('Are you sure you want to delete this student?')) {
            // Send GET request to delete student using axios
            axios.get(`student_api.php?id=${studentId}`)
                .then(response => {
                    const data = response.data;
                    alert(data.message);
                    if (data.message === 'Student deleted successfully.') {
                        // Remove the student row from the table
                        const row = document.getElementById(`student-${studentId}`);
                        row.parentNode.removeChild(row);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while trying to delete the student.');
                });
        }
    }

    function editStudent(button) {
    const id = button.dataset.id;
    const lastname = button.dataset.lastname;
    const firstname = button.dataset.firstname;
    const middlename = button.dataset.middlename;
    const college = button.dataset.college;
    const program = button.dataset.program;
    const year = button.dataset.year;

    document.getElementById('edit-id').value = id;
    document.getElementById('edit-lastname').value = lastname;
    document.getElementById('edit-firstname').value = firstname;
    document.getElementById('edit-middlename').value = middlename;
    document.getElementById('edit-college').value = college;
    document.getElementById('edit-program').value = program;
    document.getElementById('edit-year').value = year;

    modal.classList.add('active');
    overlay.classList.add('active');
}


    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            editStudent(this);
        });
    });

    document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevents the default form submission

    const formData = new FormData(this);
    
    // Add the 'edit' flag to indicate this is an update, not a new entry
    formData.append('edit', true);  // Add the edit flag

    // Get the student ID from the form (or some other source)
    // For debugging: check what is being sent in the form data
    formData.forEach((value, key) => {
        console.log(`${key}: ${value}`);
    });
    const studentId = formData.get("student_id");  // Ensure you have an input with this id
    console.log("student id",studentId );

    // Use axios to send the form data with the studentId in the URL
    axios.post(`student_api.php?id=${studentId}`, formData)
        .then(response => {
            const data = response.data;
            alert(data.message);
            if (data.message === 'Student information updated successfully.') {
                location.reload();  // Reload to show updated data
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the student.');
        });
});




</script>
</body>
</html>
