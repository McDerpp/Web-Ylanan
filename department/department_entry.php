<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Entry</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>New Department Entry</h2>
        <div id="message"></div>

        <form id="departmentForm">
            <!-- College Dropdown -->
            <label for="deptcollid">College:</label>
            <select id="deptcollid" name="deptcollid" required>
                <option value="" disabled selected>Select College</option>
            </select>

            <label for="deptID">Department ID:</label>
            <input type="text" id="deptID" name="deptID" required>

            <!-- Department Full Name -->
            <label for="deptfullname">Department Full Name:</label>
            <input type="text" id="deptfullname" name="deptfullname" required>

            <!-- Department Short Name -->
            <label for="deptshortname">Department Short Name:</label>
            <input type="text" id="deptshortname" name="deptshortname" required>

            <div class="buttons">
                <input type="submit" value="Save Department">
                <button type="reset">Clear Entries</button>
                <button type="button" onclick="window.location.href='department_dashboard.php';">Cancel</button>
            </div>
        </form>

        <div id="response"></div>
    </div>

    <script>
        // Call this function on page load
        window.onload = function() {
            loadColleges();
        };

        // Load Colleges into the Dropdown
        function loadColleges() {
            axios.get('../student/student_api.php', {
                params: {
                    type: 'colleges'
                }
            })
            .then(function(response) {
                console.log('Response data:', response.data); // Debugging the response data

                if (Array.isArray(response.data) && response.data.length > 0) {
                    const collegeSelect = document.getElementById('deptcollid');
                    response.data.forEach(function(college) {
                        const option = document.createElement('option');
                        option.value = college.collid;  // Set the college id as value
                        option.textContent = college.collfullname;  // Set the college full name as text
                        collegeSelect.appendChild(option);
                    });
                } else {
                    console.error("No colleges found or response is not an array.");
                }
            })
            .catch(function(error) {
                console.error('Error loading colleges:', error);
            });
        }

        // Form Submission
        document.getElementById('departmentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting the usual way

            const formData = new FormData(this);

            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });

            console.log('Form data:', formDataObj);

            axios.post('department_api.php', formDataObj)
                .then(function(response) {
                    document.getElementById('response').innerText = response.data.message;
                })
                .catch(function(error) {
                    document.getElementById('response').innerText = 'An error occurred: ' + error.message;
                });
        });
    </script>
</body>
</html>
