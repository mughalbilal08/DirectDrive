<?php
include('connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Determine user role
$user_role = $_SESSION['role'] ?? 'driver';

// Restrict access to only drivers
if ($user_role !== 'driver') {
    echo '<script>alert("Access denied. This page is for drivers only."); window.location.href = "login.php";</script>';
    exit();
}

// Use the same session variable for ID as other pages
$user_id = $_SESSION['id'];

// Fetch the user's subscription status from the database
$sql = "SELECT subscription_type, subscription_status FROM driver_details WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subscription_type = 'basic';
$subscription_status = 'inactive';

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $subscription_type = !empty($user['subscription_type']) ? $user['subscription_type'] : 'basic';
    $subscription_status = !empty($user['subscription_status']) ? $user['subscription_status'] : 'inactive';
} else {
    error_log("No subscription data found for user ID: $user_id");
}
$stmt->close();

// Check if payment credentials exist
$has_credentials = false;
$sql = "SELECT * FROM payment_credentials WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $has_credentials = true;
}
$stmt->close();

// Define plan prices (to display price of selected plan)
$plan_prices = [
    'basic' => 0,
    'standard' => 10,
    'premium' => 20
];

// Fetch additional driver metrics
// Total trips
$sql_trips = "SELECT COUNT(*) as total_trips FROM booking_detail WHERE driver_id = ? AND ride_status = 'Completed'";
$stmt_trips = $conn->prepare($sql_trips);
$stmt_trips->bind_param("i", $user_id);
$stmt_trips->execute();
$result_trips = $stmt_trips->get_result();
$row_trips = $result_trips->fetch_assoc();
$total_trips = $row_trips['total_trips'] ?? 0;
$stmt_trips->close();

// Total earnings
$sql_earnings = "SELECT SUM(payment) as total_earnings FROM booking_detail WHERE driver_id = ? AND ride_status = 'Completed'";
$stmt_earnings = $conn->prepare($sql_earnings);
$stmt_earnings->bind_param("i", $user_id);
$stmt_earnings->execute();
$result_earnings = $stmt_earnings->get_result();
$row_earnings = $result_earnings->fetch_assoc();
$total_earnings = $row_earnings['total_earnings'] ?? 0;
$stmt_earnings->close();

// Pending notifications
$sql_notifications = "SELECT COUNT(*) as pending_notifications FROM notifications WHERE DriverId = ? AND status = 'Pending'";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->bind_param("i", $user_id);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->get_result();
$row_notifications = $result_notifications->fetch_assoc();
$pending_notifications = $row_notifications['pending_notifications'] ?? 0;
$stmt_notifications->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Direct Drive</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #1A202C;
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .grid-container {
            display: flex;
            width: 100%;
            position: relative;
        }
        .header {
            background-color: #2D3748;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .menu-icon {
            cursor: pointer;
            color: #7F9CF5;
        }
        .header-right label {
            font-size: 1.1rem;
            color: #CBD5E0;
        }
        #sidebar {
            width: 250px;
            min-width: 250px;
            background-color: #2D3748;
            padding-top: 70px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 999;
            transition: transform 0.3s ease;
        }
        #sidebar.closed {
            transform: translateX(-190px);
            width: 250px;
        }
        .main-container {
            width: 70%;
            padding: 90px 40px 40px;
            background-color: #1A202C;
            margin-left: 310px;
        }
        .main-title h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #7F9CF5;
            text-align: center;
            margin-bottom: 20px;
            animation: slideIn 0.5s ease;
        }
        .welcome-message {
            text-align: center;
            font-size: 1.2rem;
            color: #CBD5E0;
            margin-bottom: 30px;
            animation: fadeIn 0.8s ease;
        }
        .subscription-card {
            background-color: #2D3748;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease;
        }
        .subscription-card .current-plan {
            font-size: 1.3rem;
            font-weight: 600;
            color: #28a745;
            background-color: #1A202C;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .subscription-card .status-message {
            font-size: 1rem;
            color: #CBD5E0;
        }
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }
        .dashboard-card {
            flex: 1;
            min-width: 200px;
            background-color: #2D3748;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 1s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .dashboard-card .icon {
            font-size: 2.5rem;
            color: #7F9CF5;
            margin-bottom: 10px;
        }
        .dashboard-card h3 {
            font-size: 1.1rem;
            color: #CBD5E0;
            margin-bottom: 10px;
        }
        .dashboard-card .value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
        }
        .upload-button {
            padding: 10px 20px;
            background-color: #805AD5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .upload-button:hover {
            background-color: #6B46C1;
            transform: scale(1.05);
        }
        .no-plan-message {
            background-color: #2D3748;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #CBD5E0;
            animation: fadeIn 0.8s ease;
        }
        .sidebar-section {
            padding: 12px 20px;
            margin: 8px 0;
            font-size: 1.1rem;
            cursor: pointer;
            color: #fff;
            background: #805AD5;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar-section:hover {
            background: #6B46C1;
        }
        .sidebar-sublist {
            display: none;
            padding-left: 20px;
        }
        .sidebar-list-item a {
            color: #CBD5E0;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .sidebar-list-item a:hover {
            color: #7F9CF5;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 1024px) {
            .main-container {
                padding: 80px 30px 30px;
            }
            .dashboard-card {
                min-width: 150px;
            }
        }
        @media (max-width: 768px) {
            .grid-container {
                flex-direction: column;
            }
            #sidebar {
                width: 100%;
                min-width: 100%;
                padding-top: 60px;
                position: relative;
                transform: none;
            }
            #sidebar.closed {
                transform: none;
                display: none;
            }
            .main-container {
                padding: 70px 20px 20px;
                margin-left: 0;
                width: 100%;
            }
            .main-title h2 {
                font-size: 1.8rem;
            }
            .dashboard-cards {
                flex-direction: column;
                align-items: center;
            }
        }
        @media (max-width: 480px) {
            .main-container {
                padding: 60px 15px 15px;
            }
            .current-plan {
                font-size: 1rem;
            }
            .subscription-card {
                padding: 15px;
            }
            .upload-button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            .main-title h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="grid-container">
        <!-- Header -->
        <header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <label style="margin-left: 35px;"><?php echo $_SESSION['name'];?></label> 
            </div>
        </header>

        <!-- Driver Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-brand">
                    Direct Drive
                </div>
                <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
            </div>
            <ul class="sidebar-list">
                <li class="sidebar-list-item"><a href="driverdashboard.php"><i class='bx bx-grid-alt'></i> Dashboard</a></li>
                <li class="sidebar-list-item"><a href="driverNotifications.php"><i class='bx bx-bell'></i> Notifications</a></li>
                
                <li class="sidebar-section" onclick="toggleSection('add-section')"><i class='bx bxs-plus-circle'></i> ADD</li>
                <ul id="add-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="add_ride_bydriver.php"><i class='bx bxs-user'></i> Add Ride</a></li>
                    <li class="sidebar-list-item"><a href="add_payment_bydriver.php"><i class='bx bxs-car'></i> Add Expenses</a></li>
                </ul>

                <li class="sidebar-section" onclick="toggleSection('view-section')"><i class='bx bxs-show'></i> VIEW</li>
                <ul id="view-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="driverincomereport.php"><i class='bx bxs-user'></i> Trip List</a></li>
                </ul>

                <li class="sidebar-section" onclick="toggleSection('report-section')"><i class='bx bxs-report'></i> REPORT</li>
                <ul id="report-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="driverexpensereport.php"><i class='bx bxs-report'></i> Expenses Report</a></li>
                </ul>

                <li class="sidebar-section" onclick="toggleSection('cash-section')"><i class='bx bxs-dollar-circle'></i> CASH</li>
                <ul id="cash-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="driverDeposits.php"><i class='bx bxs-user'></i> Deposits</a></li>
                </ul>

                <li class="sidebar-list-item"><a href="subscriptions.php"><i class='bx bxs-credit-card'></i> Subscriptions</a></li>
                <li class="sidebar-list-item"><a href="logout.php"><i class='bx bx-log-out'></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Driver Dashboard</h2>
            </div>

            <div class="welcome-message">
                Welcome, <?php echo $_SESSION['name']; ?>! Here's your overview.
            </div>

            <!-- Subscription Status -->
            <div class="subscription-card">
                <p class="current-plan">Current Plan: <?php echo ucfirst($subscription_type); ?> (Status: <?php echo ucfirst($subscription_status); ?>)</p>
                <?php if ($subscription_status != 'active' && !isset($_SESSION['selected_plan'])) { ?>
                    <p class="status-message">No plan is selected. Please select a plan from <a href="subscriptions.php" style="color: #7F9CF5; text-decoration: underline;">Subscriptions</a>.</p>
                <?php } elseif (isset($_SESSION['selected_plan']) && $subscription_status != 'active' && $has_credentials) { ?>
                    <p class="status-message">Selected Plan: <?php echo ucfirst($_SESSION['selected_plan']); ?> (Price: $<?php echo $plan_prices[$_SESSION['selected_plan']] ?? 0; ?>/month)</p>
                    <form method="POST" action="upload_receipt.php">
                        <button type="submit" class="upload-button" name="upload_receipt">Upload Receipt for Admin Approval</button>
                    </form>
                    <p class="status-message">Receipt will be generated automatically for admin review.</p>
                <?php } elseif ($subscription_status == 'active') { ?>
                    <p class="status-message">Your subscription is active. Enjoy your plan!</p>
                <?php } elseif (!$has_credentials) { ?>
                    <p class="status-message">Please add payment credentials in <a href="payment.php" style="color: #7F9CF5; text-decoration: underline;">Payment</a> to proceed.</p>
                <?php } ?>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="icon"><i class='bx bxs-car'></i></div>
                    <h3>Total Trips</h3>
                    <div class="value"><?php echo $total_trips; ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class='bx bxs-dollar-circle'></i></div>
                    <h3>Total Earnings</h3>
                    <div class="value">$<?php echo number_format($total_earnings, 2); ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class='bx bxs-bell'></i></div>
                    <h3>Pending Notifications</h3>
                    <div class="value"><?php echo $pending_notifications; ?></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="myScript.js"></script>
    <script src="dashboardscript.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const isClosed = localStorage.getItem('sidebarClosed') === 'true';

            if (isClosed) {
                sidebar.classList.add('closed');
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('closed');
                }
            });
        });

        function openSidebar() {
            document.getElementById('sidebar').classList.remove('closed');
            localStorage.setItem('sidebarClosed', 'false');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.add('closed');
            localStorage.setItem('sidebarClosed', 'true');
        }

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === 'block') {
                section.style.display = 'none';
            } else {
                section.style.display = 'block';
            }
        }
    </script>
</body>
</html>