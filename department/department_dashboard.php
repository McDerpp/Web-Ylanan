<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .container {
            width: 80%;
            max-height: 600px;
            overflow-y: auto;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table th,
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            width: 400px;
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

        .modal-header {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .modal-actions {
            margin-top: 20px;
            text-align: right;
        }

        .btn-close {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-save {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Departments Dashboard</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        <button onclick="window.location.href='department_entry.php'" class="btn-new-entry">New Department
            Entry</button>

        <!-- College Dropdown -->
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Department rows will be inserted here dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="modal" id="edit-modal">
        <div class="modal-header">Edit Department</div>
        <form id="edit-form">
            <label for="edit-deptid">Department ID:</label>
            <input type="text" id="edit-deptid" required>
            <label for="edit-deptfullname">Full Name:</label>
            <input type="text" id="edit-deptfullname" required>
            <br><br>
            <label for="edit-deptshortname">Short Name:</label>
            <input type="text" id="edit-deptshortname" required>
            <label for="edit-college">College:</label>
            <select id="edit-college" name="edit-college" required>
                <option value="" disabled selected>Select College</option>
            </select>


            <div class="modal-actions">
                <button type="button" class="btn-close" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>

    <script>
        // Function to load colleges from the server
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
                        alert('Failed to delete department.');
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
            document.getElementById('modal-overlay').style.display = 'block';
            document.getElementById('edit-modal').style.display = 'block';
        }


        // Function to close the modal
        function closeModal() {
            document.getElementById('modal-overlay').style.display = 'none';
            document.getElementById('edit-modal').style.display = 'none';
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
    </script>
</body>

</html>