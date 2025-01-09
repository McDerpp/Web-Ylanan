<?php
include '../header.php';
include 'program_edit.php';


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
    <link rel="stylesheet" href="../css/dashboards.css">
    <link rel="stylesheet" href="../css/modal.css">

    <style>


    </style>


</head>

<body>
    <div class="container">
        <div class="dashBoardTitle">
            <h2>Programs Dashboard</h2>

        </div>
        <button onclick="window.location.href='program_entry.php'" class="btn-new-entry">New Program Entry</button>

        <label for="college">College:</label>
        <select id="college" name="college" required>
            <option value="" disabled selected>Select College</option>
        </select>


        <label for="college">Department:</label>
        <select id="college" name="college" required>
            <option value="" disabled selected>Select Department</option>
        </select>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Short Name</th>
                    <th>College</th>
                    <th>Department</th>
                    <th class="actions-header">Actions</th>

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
                        <td>
                            <button class="btn-edit"
                                onclick="openModal(<?php echo $program['progid']; ?>, '<?php echo htmlspecialchars($program['progfullname']); ?>', '<?php echo htmlspecialchars($program['progshortname']); ?>', '<?php echo htmlspecialchars($program['college_name']); ?>', '<?php echo htmlspecialchars($program['department_name']); ?>')">Edit</button>
                            <button class="btn-action btn-delete"
                                onclick="deleteProgram(<?php echo $program['progid']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>




    <script>

        function loadColleges() {
            axios.get('../student/student_api.php', {
                params: {
                    type: 'colleges'
                }
            })
                .then(function (response) {
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        const collegeSelect = document.getElementById('college');
                        response.data.forEach(function (college) {
                            const option = document.createElement('option');
                            option.value = college.collid;
                            option.textContent = college.collfullname;
                            collegeSelect.appendChild(option);
                        });
                    }
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        const collegeSelect = document.getElementById('edit-college');
                        response.data.forEach(function (college) {
                            const option = document.createElement('option');
                            option.value = college.collid;
                            option.textContent = college.collfullname;
                            collegeSelect.appendChild(option);
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading colleges:', error);
                });
        }


        function loadDepartments(college) {
            console.log("college-->", college);
            axios.get('department_api.php', {
                params: { college: college }
            })
                .then(function (response) {
                    const tableBody = document.getElementById('department-table').getElementsByTagName('tbody')[0];
                    tableBody.innerHTML = '';

                    if (Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function (department) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
        <td>${department.deptid}</td>
        <td>${department.deptfullname}</td>
        <td>${department.deptshortname}</td>
        <td>
            <button class='btn-edit' 
                onclick='openModal(${department.deptid}, "${department.deptfullname}", "${department.deptshortname}", ${department.deptcollid})'>
                Edit
            </button>
            <button class='btn-delete' onclick='deleteDepartment(${department.deptid})'>Delete</button>
        </td>
    `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="4" style="text-align: center;">No departments found for the selected college.</td>`;
                        tableBody.appendChild(row);
                    }
                })
                .catch(function (error) {
                    const tableBody = document.getElementById('department-table').getElementsByTagName('tbody')[0];
                    tableBody.innerHTML = '';
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="4" style="text-align: center;">Error loading departments.</td>`;
                    tableBody.appendChild(row);

                    console.error('Error loading departments:', error);
                });
        }








        function openModal(progId, progFullName, progShortName, collegeName, deptName) {
            document.getElementById('modalProgid').value = progId;
            document.getElementById('modalProgFullName').value = progFullName;
            document.getElementById('modalProgShortName').value = progShortName;

            // Populate the College select dropdown
            fetch('program_api.php?action=fetch_colleges')
                .then(response => response.json())
                .then(colleges => {
                    const collegeSelect = document.getElementById('modalCollegeId');
                    collegeSelect.innerHTML = ''; // Clear previous options
                    colleges.forEach(college => {
                        const option = document.createElement('option');
                        option.value = college.collid;
                        option.textContent = college.collfullname;
                        if (college.collfullname === collegeName) option.selected = true;
                        collegeSelect.appendChild(option);
                    });

                    // Populate the Department select dropdown after college is selected
                    const collegeId = collegeSelect.value;
                    fetch(`program_api.php?action=fetch_departments&college_id=${collegeId}`)
                        .then(response => response.json())
                        .then(departments => {
                            const deptSelect = document.getElementById('modalDeptId');
                            deptSelect.innerHTML = ''; // Clear previous options
                            departments.forEach(department => {
                                const option = document.createElement('option');
                                option.value = department.deptid;
                                option.textContent = department.deptfullname;
                                if (department.deptfullname === deptName) option.selected = true;
                                deptSelect.appendChild(option);
                            });
                        });
                });

            document.getElementById('editModal').style.display = "block";
        }

        function onCollegeChange(event) {
            const selectedCollege = event.target.value;
            loadDepartments(selectedCollege);
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadColleges();
            const collegeSelect = document.getElementById('college');
            collegeSelect.addEventListener('change', onCollegeChange);
        });


        // Function to handle form submission (PUT request)
        function submitForm(event) {
            event.preventDefault(); // Prevent default form submission

            const progId = document.getElementById('modalProgid').value;
            const progFullName = document.getElementById('modalProgFullName').value;
            const progShortName = document.getElementById('modalProgShortName').value;
            const collegeId = document.getElementById('modalCollegeId').value;
            const deptId = document.getElementById('modalDeptId').value;

            fetch('program_api.php', {
                method: 'PUT', // The method should still be PUT for the API
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    progid: progId,
                    progfullname: progFullName,
                    progshortname: progShortName,
                    progcollid: collegeId,
                    progcolldeptid: deptId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Program updated successfully.');
                        closeModal(); // Close the modal
                        location.reload(); // Refresh the page
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred: ' + error.message);
                });
        }


        // Function to close the modal
        function closeModal() {
            document.getElementById('editModal').style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function (event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        };

        // Delete Program function
        function deleteProgram(progId) {
            if (confirm('Are you sure you want to delete this program?')) {
                fetch(`program_api.php?action=delete_program&progid=${progId}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Program deleted successfully.');
                            location.reload(); // Refresh the page to update the table
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('An error occurred: ' + error.message);
                    });
            }



        }
    </script>

</body>

</html>