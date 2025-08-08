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

// Check if the plan is coming from POST (from subscriptions.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan'])) {
    $_SESSION['selected_plan'] = $_POST['plan'];
    echo "Debug: Plan set from POST: " . htmlspecialchars($_SESSION['selected_plan']) . "<br>";
}

// Get the selected plan from the session
if (!isset($_SESSION['selected_plan'])) {
    echo "Debug: No selected_plan in session. Redirecting...<br>";
    echo '<script>alert("No plan selected. Please select a plan first."); window.location.href = "subscriptions.php";</script>';
    exit();
}
$selected_plan = $_SESSION['selected_plan'];

// Define plan prices
$plan_prices = [
    'basic' => 0,
    'standard' => 10,
    'premium' => 20
];

// Get the price of the selected plan
$plan_price = $plan_prices[$selected_plan] ?? 0;

// Check if payment credentials already exist
$has_credentials = false;
$sql = "SELECT * FROM payment_credentials WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$credentials = $result->fetch_assoc();
if ($result->num_rows > 0) {
    $has_credentials = true;
}
$stmt->close();

// Debug: Check session and POST variables
echo "Debug: Session Variables: <pre>";
var_dump($_SESSION);
echo "</pre>";
echo "Debug: POST Variables: <pre>";
var_dump($_POST);
echo "</pre>";

// Handle payment credentials submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_credentials'])) {
    $card_number = $_POST['card_number'];
    $card_holder = $_POST['card_holder'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // Basic validation
    if (!preg_match("/^\d{16}$/", $card_number) || !preg_match("/^[a-zA-Z ]+$/", $card_holder) || 
        !preg_match("/^\d{2}\/\d{2}$/", $expiry_date) || !preg_match("/^\d{3}$/", $cvv)) {
        echo '<script>alert("Invalid payment details. Please check your input."); window.location.href = "payment.php";</script>';
        exit();
    }

    // Save or update payment credentials
    if ($has_credentials) {
        $sql = "UPDATE payment_credentials SET card_number = ?, card_holder = ?, expiry_date = ?, cvv = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $card_number, $card_holder, $expiry_date, $cvv, $user_id);
    } else {
        $sql = "INSERT INTO payment_credentials (user_id, card_number, card_holder, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $card_number, $card_holder, $expiry_date, $cvv);
    }

    if ($stmt->execute()) {
        echo '<script>alert("Payment credentials saved successfully! You can now proceed with payment on the dashboard."); window.location.href = "driverdashboard.php";</script>';
    } else {
        echo '<script>alert("Failed to save payment credentials. Please try again."); window.location.href = "payment.php";</script>';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment Credentials - Direct Drive</title>

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
            margin-top: -180px;
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
        .payment-header {
            text-align: center;
            margin-right: 110px;
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease;
        }
        .payment-details {
            font-size: 1.2rem;
            font-weight: 600;
            color: #28a745;
            background-color: #2D3748;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .payment-form-container {
            display: flex;
            justify-content: center;
            padding: 10px 0;
            width: 100%;
        }
        .payment-form {
            background-color: #2D3748;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .payment-form:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .payment-form h5 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #7F9CF5;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 1rem;
            color: #CBD5E0;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #4A5568;
            border-radius: 5px;
            background-color: #1A202C;
            color: #fff;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #7F9CF5;
            box-shadow: 0 0 5px rgba(127, 156, 245, 0.3);
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
            .payment-form {
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
            .payment-form h5 {
                font-size: 1.4rem;
            }
            .form-group label {
                font-size: 0.9rem;
            }
            .form-group input {
                font-size: 0.9rem;
            }
            .btn-primary {
                padding: 10px;
                font-size: 1rem;
            }
            .payment-details {
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
                <h2>Add Payment Credentials</h2>
            </div>

            <!-- Payment Details -->
            <div class="payment-header">
                <p class="payment-details">
                    Selected Plan: <?php echo ucfirst($selected_plan); ?> (Price: $<?php echo $plan_price; ?>/month)
                </p>
            </div>

            <!-- Payment Credentials Form -->
            <div class="payment-form-container">
                <div class="payment-form">
                    <h5>Enter Payment Details</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="plan" value="<?php echo htmlspecialchars($selected_plan); ?>">
                        <input type="hidden" name="user_role" value="driver">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" value="<?php echo $has_credentials ? $credentials['card_number'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="card_holder">Card Holder Name</label>
                            <input type="text" id="card_holder" name="card_holder" placeholder="John Doe" value="<?php echo $has_credentials ? $credentials['card_holder'] : ''; ?>" required>
                        </div>
                        <div class="form-group" style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" value="<?php echo $has_credentials ? $credentials['expiry_date'] : ''; ?>" required>
                            </div>
                            <div style="flex: 1;">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" value="<?php echo $has_credentials ? $credentials['cvv'] : ''; ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="save_credentials" class="btn btn-primary"><?php echo $has_credentials ? 'Update Credentials' : 'Save Credentials'; ?></button>
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