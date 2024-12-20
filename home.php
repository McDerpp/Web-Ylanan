
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin: 20px;
        }
        .image-link {
            margin: 10px;
            text-decoration: none;
            color: black;
        }
        .image-link img {
            width: 300px;
            height: 200px;
            border: 2px solid #ccc;
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .image-link img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <h1>Welcome to the Home Page</h1>
    <div class="container">
        <a href="student/student_dashboard.php" class="image-link">
            <img src="image1.jpg" alt="Image 1">
            <p>Student Dashboard</p>
        </a>
        <a href="college/college_dashboard.php" class="image-link">
            <img src="image2.jpg" alt="Image 2">
            <p>College Dashboard</p>
        </a>
        <a href="department/department_dashboard.php" class="image-link">
            <img src="image3.jpg" alt="Image 3">
            <p>Deparment Dashboard</p>
        </a>

        </a>
        <a href="program/program_dashboard.php" class="image-link">
            <img src="image3.jpg" alt="Image 3">
            <p>Program Dashboard</p>
        </a>
    </div>
</body>
</html>