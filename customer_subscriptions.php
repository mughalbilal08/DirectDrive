<?php
include('connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Determine user role
$user_role = $_SESSION['role'] ?? 'customer';

// Restrict access to only customers
if ($user_role !== 'customer') {
    echo '<script>alert("Access denied. This page is for customers only."); window.location.href = "login.php";</script>';
    exit();
}

// Use the same session variable for ID as customer_dashboard.php
$user_id = $_SESSION['id'];

// Fetch the user's current subscription from the database
$sql = "SELECT subscription_type, subscription_status FROM user_accounts WHERE id = ? AND role = 'customer'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_subscription = $user['subscription_type'] ?? 'basic';
$subscription_status = $user['subscription_status'] ?? 'active';

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Subscriptions - Direct Drive</title>

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
            padding: 20px 40px 40px; /* Reduced top padding */
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
        .subscription-header {
            text-align: center;
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
            justify-content: center;
            gap: 20px;
            padding: 10px 0;
            width: 100%;
        }
        .plan-card {
            flex: 1;
            max-width: 300px;
            background-color: #2D3748;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .plan-card.current-plan-card::after {
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
        .plan-card h5 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #7F9CF5;
            margin-bottom: 15px;
            text-align: center;
        }
        .plan-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            color: #CBD5E0;
        }
        .plan-card ul li {
            font-size: 1rem;
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }
        .plan-card ul li:before {
            content: '✔';
            color: #28a745;
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
                padding: 20px 30px 30px;
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
                padding: 20px 20px 20px;
                margin-left: 0;
                width: 100%;
            }
            .subscription-plans {
                flex-direction: column;
                gap: 20px;
            }
            .plan-card {
                max-width: none;
                width: 100%;
            }
            .main-title h2 {
                font-size: 1.8rem;
            }
        }
        @media (max-width: 480px) {
            .main-container {
                padding: 20px 15px 15px;
            }
            .plan-card h5 {
                font-size: 1.4rem;
            }
            .plan-card ul li {
                font-size: 0.9rem;
            }
            .btn-primary {
                padding: 10px;
                font-size: 1rem;
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

        <!-- Customer Sidebar -->
        <?php include 'customerSideBar.php'; ?>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Customer Subscriptions</h2>
            </div>

            <div class="welcome-message">
                Manage your subscription plan, <?php echo $_SESSION['name']; ?>!
            </div>

            <!-- Current Subscription -->
            <div class="subscription-header">
                <p class="current-plan">Current Plan: <?php echo ucfirst($current_subscription); ?> (Subscription Status: <?php echo ucfirst($subscription_status); ?>)</p>
            </div>

            <!-- Customer Subscription Plans -->
            <div class="subscription-plans">
                <!-- Basic Plan -->
                <div class="plan-card <?php echo $current_subscription == 'basic' ? 'current-plan-card' : ''; ?>">
                    <h5>Basic (Pay-Per-Ride)</h5>
                    <ul>
                        <li>No subscription required</li>
                        <li>Pay for each ride individually</li>
                    </ul>
                    <form method="POST" action="paymentC.php">
                        <input type="hidden" name="plan" value="basic">
                        <input type="hidden" name="user_role" value="customer">
                        <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'basic' ? 'disabled' : ''; ?>>
                            <?php echo $current_subscription == 'basic' ? 'Selected' : 'Select'; ?>
                        </button>
                    </form>
                </div>
                <!-- Standard Plan -->
                <div class="plan-card <?php echo $current_subscription == 'standard' ? 'current-plan-card' : ''; ?>">
                    <h5>Standard ($15/month)</h5>
                    <ul>
                        <li>Discounted rides</li>
                        <li>Option to book a specific driver</li>
                    </ul>
                    <form method="POST" action="paymentC.php">
                        <input type="hidden" name="plan" value="standard">
                        <input type="hidden" name="user_role" value="customer">
                        <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'standard' ? 'disabled' : ''; ?>>
                            <?php echo $current_subscription == 'standard' ? 'Selected' : 'Select'; ?>
                        </button>
                    </form>
                </div>
                <!-- Premium Plan -->
                <div class="plan-card <?php echo $current_subscription == 'premium' ? 'current-plan-card' : ''; ?>">
                    <h5>Premium ($25/month)</h5>
                    <ul>
                        <li>Priority booking</li>
                        <li>Free cancellation</li>
                        <li>AI-based smart scheduling</li>
                    </ul>
                    <form method="POST" action="paymentC.php">
                        <input type="hidden" name="plan" value="premium">
                        <input type="hidden" name="user_role" value="customer">
                        <button type="submit" class="btn btn-primary" <?php echo $current_subscription == 'premium' ? 'disabled' : ''; ?>>
                            <?php echo $current_subscription == 'premium' ? 'Selected' : 'Select'; ?>
                        </button>
                    </form>
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
    </script>
</body>
</html>