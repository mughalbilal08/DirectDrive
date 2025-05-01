<?php
include('connect.php'); // Include the database connection
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

// Get the selected plan from the form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['plan']) || !isset($_POST['user_role'])) {
    echo '<script>alert("Invalid request. Please select a plan."); window.location.href = "customer_subscriptions.php";</script>';
    exit();
}

$selected_plan = $_POST['plan'];
$user_role = $_POST['user_role'];

// Validate user role
if ($user_role !== 'customer') {
    echo '<script>alert("Invalid user role."); window.location.href = "customer_subscriptions.php";</script>';
    exit();
}

// Define plan prices (you can store these in a database if preferred)
$plan_prices = [
    'basic' => 0,    // Pay-Per-Ride (Free)
    'standard' => 15, // $15/month (example price)
    'premium' => 25   // $25/month (example price)
];

// Get the price of the selected plan
$plan_price = $plan_prices[$selected_plan] ?? 0;

// Handle payment form submission (mock for now)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    // In a real application, integrate with a payment gateway (e.g., Stripe, PayPal) here
    // For now, we'll mock the payment process and update the subscription

    // Update the customer's subscription in the database
    $sql = "UPDATE user_accounts SET subscription_type = ?, subscription_status = 'active' WHERE id = ? AND role = 'customer'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $selected_plan, $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to customer_subscriptions.php with a success message
    echo '<script>alert("Payment successful! Your subscription has been updated."); window.location.href = "customer_subscriptions.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Concord Transport</title>

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
            position: relative; /* For sidebar positioning */
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
            padding-top: 70px; /* Space for fixed header */
            position: fixed; /* Overlay sidebar */
            top: 0;
            left: 0;
            height: 100%;
            z-index: 999; /* Below header but above content */
            transition: transform 0.3s ease;
        }
        #sidebar.closed {
            transform: translateX(-190px); /* Hide most of the sidebar, leaving 60px visible */
            width: 250px; /* Maintain width for smooth animation */
        }
        .main-container {
            width: 70%;
            margin-left: 310px;
            padding: 90px 40px 40px; /* Extra top padding for fixed header */
            background-color: #1A202C;
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
            max-width: 500px; /* Limit form width */
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
                position: relative; /* Stack sidebar normally on small screens */
                transform: none; /* Disable transform on small screens */
            }
            #sidebar.closed {
                transform: none; /* Disable transform on small screens */
                display: none; /* Optionally hide sidebar on small screens */
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
                <label><?php echo $_SESSION['name']; ?></label>
            </div>
        </header>

        <!-- Customer Sidebar -->
        <?php include 'customerSideBar.php'; ?>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Payment</h2>
            </div>

            <!-- Payment Details -->
            <div class="payment-header">
                <p class="payment-details">
                    Selected Plan: <?php echo ucfirst($selected_plan); ?> (Price: $<?php echo $plan_price; ?>/month)
                </p>
            </div>

            <!-- Payment Form -->
            <div class="payment-form-container">
                <div class="payment-form">
                    <h5>Enter Payment Details</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="plan" value="<?php echo htmlspecialchars($selected_plan); ?>">
                        <input type="hidden" name="user_role" value="customer">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>
                        <div class="form-group">
                            <label for="card_holder">Card Holder Name</label>
                            <input type="text" id="card_holder" name="card_holder" placeholder="John Doe" required>
                        </div>
                        <div class="form-group" style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
                            </div>
                            <div style="flex: 1;">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" required>
                            </div>
                        </div>
                        <button type="submit" name="process_payment" class="btn btn-primary">Pay Now</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="myScript.js"></script>
    <script src="dashboardscript.js"></script>
    <script>
        // Sidebar state management
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