<?php
include '../header.php';
include 'college_edit.php';


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
    <title>Colleges Dashboard</title>
    <link rel="stylesheet" href="../css/dashboards.css">
    <link rel="stylesheet" href="../css/modal.css">

    <style>


    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="dashBoardTitle">
            <h2>Colleges Dashboard</h2>
            <!-- <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div> -->
        </div>
        <button onclick="window.location.href='college_entry.php'" class="btn-new-entry">New College Entry</button>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Short Name</th>
                    <th class="actions-header">Actions</th>

                </tr>
            </thead>

            <tbody>
                <?php foreach ($colleges as $college): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($college['collid']); ?></td>
                        <td><?php echo htmlspecialchars($college['collfullname']); ?></td>
                        <td><?php echo htmlspecialchars($college['collshortname']); ?></td>
                        <td>
                            <!-- Edit button triggers modal -->
                            <!-- Edit button triggers modal -->
                            <button class="btn-edit"
                                onclick="openModal(<?php echo $college['collid']; ?>, '<?php echo htmlspecialchars($college['collfullname']); ?>', '<?php echo htmlspecialchars($college['collshortname']); ?>')">Edit</button>

                            <!-- Delete button that sends a request to delete the college -->
                            <form action="college_api.php" method="POST" style="display:inline;">
                                <input type="hidden" name="collid" value="<?php echo $college['collid']; ?>">
                                <button type="submit" class="btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this college?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



    <script>
        // Function to open the modal
        function openModal(collid, collfullname, collshortname) {
            document.getElementById("modalCollId").value = collid;
            document.getElementById("modalCollFullname").value = collfullname;
            document.getElementById("modalCollShortname").value = collshortname;
            document.getElementById("editModal").style.display = "block";
        }


        // Close modal after updating college
        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

        function deleteCollege(collid) {
            if (confirm('Are you sure you want to delete this college?')) {
                // Send a POST request using Axios
                axios.post('college_api.php', {
                    collid: collid
                })
                    .then(function (response) {
                        if (response.data.status === 'success') {
                            alert(response.data.message);
                            // Remove the deleted college row from the table
                            const row = document.getElementById('college-row-' + collid);
                            if (row) {
                                row.remove();  // Remove the row from the DOM
                            }
                        } else {
                            alert(response.data.message);
                        }
                    })
                    .catch(function (error) {
                        console.error('Error object:', error);

                        if (error.response) {
                            // The server responded with a status code outside the 2xx range
                            console.error('Server responded with:', error.response.data);
                            alert(error.response.data.message || 'Error occurred on the server.');
                        } else if (error.request) {
                            // No response was received
                            console.error('No response received:', error.request);
                            alert('No response from server. Please check the network connection.');
                        } else {
                            // Other errors (e.g., axios setup issues)
                            console.error('Error setting up request:', error.message);
                            alert('An unexpected error occurred: ' + error.message);
                        }
                    });

            }
        }












        document.getElementById('edit-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const collid = document.getElementById('modalCollId').value;
            const collfullname = document.getElementById('modalCollFullname').value;
            const collshortname = document.getElementById('modalCollShortname').value;

            console.log("collid ->", collid);
            console.log("collfullname ->", collfullname);
            console.log("collshortname ->", collshortname);

            axios.put('college_api.php', {
                collid: collid,
                collfullname: collfullname,
                collshortname: collshortname,
            })
                .then(function (response) {
                    alert(response.data.message);
                    closeModal();
                    location.reload();  // This will reload the page after a successful update
                })
                .catch(function (error) {
                    if (error.response) {
                        // The request was made, but the server responded with a status code that falls out of the range of 2xx
                        console.error('Error response:', error.response);
                        alert('Error: ' + error.response.data.message);
                    } else if (error.request) {
                        // The request was made, but no response was received
                        console.error('Error request:', error.request);
                        alert('No response from server.');
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        console.error('Error message:', error.message);
                        alert('Request setup error: ' + error.message);
                    }
                });
        });



        // Close the modal if the user clicks outside of it
        window.onclick = function (event) {
            if (event.target == document.getElementById("editModal")) {
                closeModal();
            }
        }
    </script>

</body>

</html>