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

// Use the same session variable for ID as driverdashboard.php
$user_id = $_SESSION['id'];

// Fetch the user's current subscription from the database
$sql = "SELECT subscription_type, subscription_status FROM driver_details WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables
$current_subscription = 'basic';
$subscription_status = 'inactive';

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $current_subscription = !empty($user['subscription_type']) ? $user['subscription_type'] : 'basic';
    $subscription_status = !empty($user['subscription_status']) ? $user['subscription_status'] : 'inactive';
} else {
    error_log("No subscription data found for user ID: $user_id");
}
$stmt->close();

// Handle plan selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan'])) {
    $_SESSION['selected_plan'] = $_POST['plan'];
    // Debug: Confirm session variable is set
    echo "Debug: Selected plan set in session: " . htmlspecialchars($_SESSION['selected_plan']) . "<br>";
    header("Location: payment.php");
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['plan'])) {
    echo "Debug: No plan received in POST data<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Subscriptions - Direct Drive</title>

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
            font-size: 1.3rem;
            font-weight: 500;
            color: #7F9CF5;
            text-align: center;
            margin-bottom: 30px;
            animation: slideIn 0.5s ease;
        }
        .subscription-header {
            text-align: center;
            margin-right: 110px;
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease;
        }
        .current-plan {
            font-size: 1.2rem;
            font-weight: 600;
            color: #28a745;
            background-color: #2D3748;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .subscription-plans {
            display: flex;
            justify-content: flex-start;
            gap: 25px;
            padding: 10px 0;
            width: 100%;
        }
        .card {
            flex: 1;
            background-color: #2D3748;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .card.current-plan-card::after {
            content: 'Current';
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #7F9CF5;
            margin-bottom: 15px;
            text-align: center;
            padding-top: 20px;
        }
        .card-text ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            color: #CBD5E0;
        }
        .card-text ul li {
            font-size: 1rem;
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }
        .card-text ul li:before {
            content: '✔';
            color: #28a745;
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }
        .card-text ul li.no-check:before {
            content: '✘';
            color: #e74c3c;
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #805AD5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover:not(:disabled) {
            background-color: #6B46C1;
            transform: scale(1.05);
        }
        .btn-primary:disabled {
            background-color: #4A5568;
            cursor: not-allowed;
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
            .card {
                flex: 1;
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
            }
            .subscription-plans {
                flex-direction: column;
                gap: 20px;
            }
            .card {
                flex: none;
                width: 100%;
            }
            .main-title h2 {
                font-size: 1.8rem;
            }
        }
        @media (max-width: 480px) {
            .main-container {
                padding: 60px 15px 15px;
            }
            .card-title {
                font-size: 1.4rem;
            }
            .card-text ul li {
                font-size: 0.9rem;
            }
            .btn-primary {
                padding: 10px;
                font-size: 1rem;
            }
            .main-title h2 {
                font-size: 1.5rem;
            }
            .current-plan {
                font-size: 1rem;
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
                <li class="sidebar-list-item"><a href="view_users.php?logout"><i class='bx bx-log-out'></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Driver Subscriptions</h2>
            </div>

            <!-- Current Subscription -->
            <div class="subscription-header">
                <p class="current-plan">Current Plan: <?php echo ucfirst($current_subscription); ?> (Subscription Status: <?php echo ucfirst($subscription_status); ?>)</p>
            </div>

            <!-- Driver Subscription Plans -->
            <div class="subscription-plans">
                <!-- Basic Plan -->
                <div class="card <?php echo $current_subscription == 'basic' ? 'current-plan-card' : ''; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title">Basic (Free)</h5>
                        <p class="card-text">
                            <ul>
                                <li>Can only accept assigned rides</li>
                                <li class="no-check">Cannot pick additional rides</li>
                            </ul>
                        </p>
                        <form method="POST" action="">
                            <input type="hidden" name="plan" value="basic">
                            <input type="hidden" name="user_role" value="driver">
                            <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'basic' ? 'disabled' : ''; ?>>
                                <?php echo $current_subscription == 'basic' ? 'Selected' : 'Select'; ?>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Standard Plan -->
                <div class="card <?php echo $current_subscription == 'standard' ? 'current-plan-card' : ''; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title">Standard ($10/month)</h5>
                        <p class="card-text">
                            <ul>
                                <li>Can pick additional rides</li>
                                <li>Visibility for better ride matching</li>
                            </ul>
                        </p>
                        <form method="POST" action="">
                            <input type="hidden" name="plan" value="standard">
                            <input type="hidden" name="user_role" value="driver">
                            <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'standard' ? 'disabled' : ''; ?>>
                                <?php echo $current_subscription == 'standard' ? 'Selected' : 'Select'; ?>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Premium Plan -->
                <div class="card <?php echo $current_subscription == 'premium' ? 'current-plan-card' : ''; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title">Premium ($20/month)</h5>
                        <p class="card-text">
                            <ul>
                                <li>First priority for high-paying rides</li>
                                <li>Option to set dynamic pricing</li>
                            </ul>
                        </p>
                        <form method="POST" action="">
                            <input type="hidden" name="plan" value="premium">
                            <input type="hidden" name="user_role" value="driver">
                            <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'premium' ? 'disabled' : ''; ?>>
                                <?php echo $current_subscription == 'premium' ? 'Selected' : 'Select'; ?>
                            </button>
                        </form>
                    </div>
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