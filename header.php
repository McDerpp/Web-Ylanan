<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']); // Get the current page name
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Header styles */
        .header {
            background-color: #333;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #444;
            width: 100%;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        /* Navigation buttons inside the header */
        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }

        .nav-buttons button {
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .nav-buttons button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .nav-buttons button:focus {
            outline: none;
        }

        .nav-buttons button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        /* Header-right layout for Welcome and Logout */
        .header-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .header-right span {
            font-size: 16px;
            margin-right: 10px;
        }

        .logout-button {
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        /* Make buttons responsive on smaller screens */
        @media (max-width: 768px) {
            .nav-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .nav-buttons button {
                width: 100%;
                font-size: 18px;
                padding: 14px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <!-- Dashboard Title on the left -->
        <h1>Dashboard</h1>

        <!-- Navigation buttons inside the header (left aligned) -->
        <div class="nav-buttons">
            <button
                onclick="window.location.href='<?php echo ($currentPage == 'home.php') ? 'home.php' : '../home.php'; ?>'"
                <?php echo ($currentPage == 'home.php') ? 'disabled' : ''; ?>>
                Home
            </button>

            <button
                onclick="window.location.href='<?php echo ($currentPage == 'home.php') ? 'student/student_dashboard.php' : '../student/student_dashboard.php'; ?>'"
                <?php echo ($currentPage == 'student_dashboard.php' || $currentPage == 'student_entry.php') ? 'disabled' : ''; ?>>
                Students
            </button>


            <button
                onclick="window.location.href='<?php echo ($currentPage == 'home.php') ? 'college/college_dashboard.php' : '../college/college_dashboard.php'; ?>'"
                <?php echo ($currentPage == 'college_dashboard.php' || $currentPage == 'college_entry.php') ? 'disabled' : ''; ?>>
                Colleges
            </button>

            <button
                onclick="window.location.href='<?php echo ($currentPage == 'home.php') ? 'department/department_dashboard.php' : '../department/department_dashboard.php'; ?>'"
                <?php echo ($currentPage == 'department_dashboard.php' || $currentPage == 'department_entry.php') ? 'disabled' : ''; ?>>

                Departments
            </button>

            <button
                onclick="window.location.href='<?php echo ($currentPage == 'home.php') ? 'program/program_dashboard.php' : '../program/program_dashboard.php'; ?>'"
                <?php echo ($currentPage == 'program_dashboard.php' || $currentPage == 'program_entry.php') ? 'disabled' : ''; ?>>

                Programs
            </button>


        </div>

        <div class="header-right">
            <span>
                Welcome,
                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!
            </span>
            <a href="../logout.php" class="logout-button">Logout</a>
        </div>
    </header>
</body>

</html>