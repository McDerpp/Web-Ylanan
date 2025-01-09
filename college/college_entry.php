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
    <title>College Entry</title>
    <link rel="stylesheet" href="../css/entry.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>New College Entry</h2>
        <!-- <div id="message"><?php echo $message; ?></div> -->

        <form id="collegeForm" method="POST">
            <label for="collfullname">College Full Name:</label>
            <input type="text" id="collfullname" name="collfullname" required>

            <label for="collshortname">College Short Name:</label>
            <input type="text" id="collshortname" name="collshortname" required>

            <div class="buttons">
                <input type="submit" value="Save College">
                <button type="reset">Clear Entries</button>
                <button type="button" onclick="window.location.href='college_dashboard.php';">Cancel</button>
            </div>
        </form>

        <div id="response"></div>
    </div>

    <script>
        document.getElementById('collegeForm').addEventListener('submit', function (event) {
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
            axios.post('college_api.php', formData)
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
    </script>
</body>

</html>