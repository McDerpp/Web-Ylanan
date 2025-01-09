<?php
include '../header.php';

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
    <title>New Program Entry</title>
    <link rel="stylesheet" href="../css/entry.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Styles for form layout */
    </style>
</head>

<body>
    <div class="container">
        <h2>New Program Entry</h2>
        <form id="programForm">
            <div class="form-group">
                <label for="progfullname">Full Program Name</label>
                <input type="text" id="progfullname" name="progfullname" required>
            </div>

            <div class="form-group">
                <label for="progshortname">Short Program Name</label>
                <input type="text" id="progshortname" name="progshortname" required>
            </div>

            <div class="form-group">
                <label for="progcollid">College:</label>
                <select id="progcollid" name="progcollid" required>
                    <option value="" disabled selected>Select College</option>
                    <?php foreach ($colleges as $college): ?>
                        <option value="<?php echo $college['collid']; ?>"><?php echo $college['collfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="progcolldeptid">Department</label>
                <select id="progcolldeptid" name="progcolldeptid" required>
                    <option value="">Select Department</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>

    <script>
        // Function to fetch departments when a college is selected
        function fetchDepartments() {
            var collegeId = document.getElementById('progcollid').value;
            var deptSelect = document.getElementById('progcolldeptid');

            // Clear existing options
            deptSelect.innerHTML = '<option value="">Select Department</option>';

            if (collegeId) {
                axios.get('program_api.php', {
                    params: {
                        action: 'fetch_departments',
                        college_id: collegeId
                    }
                })
                    .then(function (response) {
                        var departments = response.data;
                        departments.forEach(function (department) {
                            var option = document.createElement('option');
                            option.value = department.deptid;
                            option.text = department.deptfullname;
                            deptSelect.appendChild(option);
                        });
                    })
                    .catch(function (error) {
                        console.error('Error fetching departments:', error);
                    });
            }
        }

        // Add event listener to load departments when a college is selected
        document.getElementById('progcollid').addEventListener('change', fetchDepartments);

        // Handle form submission via AJAX
        document.getElementById('programForm').addEventListener('submit', function (event) {
            event.preventDefault();

            var formData = new FormData(this);

            axios.post('program_api.php?action=insert_program', formData)
                .then(function (response) {
                    alert(response.data.message); // Display success or error message
                })
                .catch(function (error) {
                    alert('Error: ' + error.response.data.message);
                });
        });
    </script>
</body>

</html>