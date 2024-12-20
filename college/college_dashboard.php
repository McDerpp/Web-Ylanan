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
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Adjust container size and add scroll */
        .container {
            width: 80%;
            /* Adjust the width to your preference */
            max-height: 600px;
            /* Set a max height for the container */
            overflow-y: auto;
            /* Enable vertical scrolling */
            margin: 0 auto;
            /* Center the container */
            padding: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        /* Style for the table inside the container */
        table {
            width: 100%;
            height: 50;
            border-collapse: collapse;
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

        /* Adjust button appearance */
        .btn-new-entry,
        .btn-edit,
        .btn-delete {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-new-entry {
            margin-bottom: 20px;
        }

        .btn-edit {
            background-color: #ff9800;
        }

        .btn-delete {
            background-color: #f44336;
        }

        .btn-logout {
            padding: 10px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        /* Make the modal content smaller */
        .modal-content {
            width: 50%;
            /* Adjust the width of the modal */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Colleges Dashboard</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        <button onclick="window.location.href='college_entry.php'" class="btn-new-entry">New College Entry</button>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Short Name</th>
                    <th>Actions</th> <!-- New column for Edit/Delete -->
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

    <!-- Modal for editing college details -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Edit College</h3>
            <form id="edit-form" action="college_api.php" method="PUT">
                <input type="hidden" name="collid" id="modalCollId">
                <div class="form-group">
                    <label for="collfullname">Full Name</label>
                    <input type="text" id="modalCollFullname" name="collfullname" required>
                </div>
                <div class="form-group">
                    <label for="collshortname">Short Name</label>
                    <input type="text" id="modalCollShortname" name="collshortname" required>
                </div>
                <button type="submit" class="btn-save">Save</button>

            </form>
        </div>
    </div>

    <script>
        // Function to open the modal
        function openModal(collid, collfullname, collshortname) {
            document.getElementById("modalCollId").value = collid;
            document.getElementById("modalCollFullname").value = collfullname;
            document.getElementById("modalCollShortname").value = collshortname;
            document.getElementById("editModal").style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById("editModal").style.display = "none";
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
            console.error('Error deleting college:', error);
            alert('There was an error deleting the college.');
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