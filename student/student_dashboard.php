<?php
include '../header.php';
include 'student_edit.php';



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
    <link rel="stylesheet" href="../css/dashboards.css">
    <link rel="stylesheet" href="../css/modal.css">


    <style>
        .modal.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="dashBoardTitle">
            <h2>Students Dashboard</h2>

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
                    <th class="actions-header">Actions</th>



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
                            <button class="btn-delete" data-id="<?php echo $student['student_id']; ?>"
                                onclick="deleteStudent(this)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <script>
        let colleges = {}; // Global variable to store colleges data
        let programs = {};

        let collegeSelected;
        let programSelected;

        let collegePicked;
        let programPicked;



        const modal = document.getElementById('edit-modal');

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
            // document.getElementById('edit-college').value = college;
            // document.getElementById('edit-program').value = program;
            document.getElementById('edit-year').value = year;

            modal.classList.add('active');
            overlay.classList.add('active');
        }

        function loadColleges() {
            axios.get('student_api.php', {
                params: {
                    type: 'colleges'
                }
            })
                .then(function (response) {
                    console.log('Response data:', response.data); // Debugging the response data

                    // Check if the response data is an array
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        const collegeSelect = document.getElementById('college');
                        response.data.forEach(function (college) {
                            const option = document.createElement('option');
                            option.value = college.collfullname; // Set college name as the option value
                            option.textContent = college.collfullname; // Set college name as the option text
                            collegeSelect.appendChild(option);
                        });
                    } else {
                        console.error("No colleges found or response is not an array.");
                    }
                })
                .catch(function (error) {
                    console.error('Error loading colleges:', error);
                });
        }

        function loadPrograms(progFullName) {

            // Clear the program dropdown before fetching new data
            const programSelect = document.getElementById('program');
            programSelect.innerHTML = ''; // Clear current options

            // Add the default "Select Program" option
            const defaultOption = document.createElement('option');
            defaultOption.textContent = 'Select Program';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            programSelect.appendChild(defaultOption);

            // Send a request to the backend to fetch programs for the selected college
            axios.get('student_api.php', {
                params: {
                    type: 'programs', // Specify that we want to get programs
                    progFullName: progFullName // Pass the college's full name
                }
            })
                .then(function (response) {
                    console.log(response.data); // Log the response data

                    // Check if programs are returned, if so, populate the dropdown
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (program) {
                            const option = document.createElement('option');
                            option.value = program.progfullname; // Program ID
                            option.textContent = program.progfullname; // Program Full Name
                            programSelect.appendChild(option);
                        });
                    } else {
                        // If no programs are found, show a "No programs available" option
                        const noProgramsOption = document.createElement('option');
                        noProgramsOption.value = '';
                        noProgramsOption.textContent = 'No programs available';
                        programSelect.appendChild(noProgramsOption);
                    }
                })
                .catch(function (error) {
                    console.error('Error fetching programs:', error);
                    // Handle error case
                    const errorOption = document.createElement('option');
                    errorOption.textContent = 'Error loading programs. Please try again.';
                    errorOption.disabled = true;
                    programSelect.appendChild(errorOption);
                });
        }



        function onCollegeChange(event) {
            const selectedCollegeId = event.target.value;
            loadPrograms(selectedCollegeId);
            collegePicked = colleges.find(college => college.collfullname == selectedCollegeID);
            console.log("test->", collegePicked);



            const programSelect = document.getElementById('program');


            // Reset the program dropdown
            programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';

            if (programs[selectedCollegeId] && programs[selectedCollegeId].length > 0) {
                programs[selectedCollegeId].forEach(function (program) {
                    const option = document.createElement('option');
                    option.value = program.progid;
                    option.textContent = program.progfullname;
                    programSelect.appendChild(option);
                });
            } else {
                const noProgramsOption = document.createElement('option');
                noProgramsOption.value = '';
                noProgramsOption.textContent = 'No programs available';
                programSelect.appendChild(noProgramsOption);
            }
        }



        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                editStudent(this);
            });
        });

        document.getElementById('edit-form').addEventListener('submit', function (e) {
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
            console.log("student id", studentId);

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


        document.addEventListener('DOMContentLoaded', function () {
            loadColleges();
            collegeSelect = document.getElementById('college');
            collegeSelect.addEventListener('change', onCollegeChange);
            console.log('College changed to:', collegeSelect.value);




            programSelected = document.getElementById('program');
            programSelected.addEventListener('change', function () {
                const selectedProgramId = programSelected.value; // Get the selected program ID
                const selectedOption = programSelected.options[programSelected.selectedIndex]; // Get the selected option
                const programPicked = selectedOption.text; // Get the program's fullname (text)

                if (selectedProgramId) {
                    console.log('Program ID:', selectedProgramId); // Program ID (progid)
                    console.log('Program Fullname:', programPicked); // Program Fullname (progfullname)
                }
            });





            document.getElementById('studentForm').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting the usual way

                // Gather form data
                const formData = new FormData(this);

                // Log form data to console for debugging
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Form data:', formDataObj);

                // Send the form data using axios
                axios.post('student_api.php', formData)
                    .then(function (response) {
                        console.log(response.data);  // Log the response data from PHP
                        document.getElementById('response').innerText = response.data.message;  // Display the message from PHP
                    })
                    .catch(function (error) {
                        // Handle error
                        console.error(error);
                        document.getElementById('response').innerText = 'An error occurred: ' + error.message;
                    });
            });



        });

    </script>
</body>

</html>