<?php
include '../header.php';
include 'department_edit.php';


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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments Dashboard</title>
    <link rel="stylesheet" href="../css/dashboards.css">
    <link rel="stylesheet" href="../css/modal.css">

    <style>

    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="dashBoardTitle">
            <h2>Departments Dashboard</h2>

        </div>
        <button onclick="window.location.href='department_entry.php'" class="btn-new-entry">New Department
            Entry</button>

        <label for="college">College:</label>
        <select id="college" name="college" required>
            <option value="" disabled selected>Select College</option>
        </select>

        <table id="department-table">
            <thead>
                <tr>
                    <th>Department ID</th>
                    <th>Full Name</th>
                    <th>Short Name</th>
                    <th class="actions-header">Actions</th>
                </tr>
            </thead>
            <tbody>
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

        // Function to load departments for the selected college
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
        function deleteDepartment(deptid) {
            console.log("deptid-->", deptid);
            if (confirm("Are you sure you want to delete this department?")) {
                axios.delete('department_api.php', {
                    params: { deptid: deptid } // Send as part of the request body
                })
                    .then(function (response) {
                        alert(response.data.message);
                        // Reload the table or refresh departments list
                        const selectedCollege = document.getElementById('college').value;
                        loadDepartments(selectedCollege);
                    })
                    .catch(function (error) {
                        console.error('Error deleting department:', error);
                        alert('Programs associated with this department exist. Delete those programs first.');
                    });
            }
        }

        // Function to open the modal
        function openModal(deptid, fullname, shortname, collegeId) {
            // Populate the modal dropdown with the correct college preselected
            loadColleges('edit-college', collegeId);

            // Set form values in the modal

            document.getElementById('edit-deptid').value = deptid;
            document.getElementById('edit-deptfullname').value = fullname;
            document.getElementById('edit-deptshortname').value = shortname;

            // Show the modal
            document.getElementById('editModal').style.display = 'block';
        }


        // Function to close the modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Event listener for form submission
        document.getElementById('edit-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const deptid = document.getElementById('edit-deptid').value;
            const deptfullname = document.getElementById('edit-deptfullname').value;
            const deptshortname = document.getElementById('edit-deptshortname').value;
            const deptcollid = document.getElementById('edit-college').value;

            axios.put('department_api.php', {
                deptid: deptid,
                deptfullname: deptfullname,
                deptshortname: deptshortname,
                deptcollid: deptcollid
            })
                .then(function (response) {
                    alert(response.data.message);
                    closeModal();
                    const collegeId = document.getElementById('college').value; // Reload main dropdown data
                    loadDepartments(collegeId);
                })
                .catch(function (error) {
                    console.error('Error updating department:', error);
                });
        });



        // Event listener for college selection
        function onCollegeChange(event) {
            const selectedCollege = event.target.value;
            loadDepartments(selectedCollege);
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadColleges();
            const collegeSelect = document.getElementById('college');
            collegeSelect.addEventListener('change', onCollegeChange);
        });

        window.onclick = function (event) {
            if (event.target == document.getElementById("editModal")) {
                closeModal();
            }
        }
    </script>
</body>

</html>