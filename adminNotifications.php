<?php
include('connect.php');
session_start();

if ($_SESSION['loggedin'] == true) {
    // Existing queries for original functionality
    $sql0 = "SELECT * FROM driver_details WHERE role = 'Driver'";
    $result0 = mysqli_query($conn, $sql0);
    $numberofDrivers = mysqli_num_rows($result0);

    $sql1 = "SELECT * FROM user_accounts WHERE role = 'customer'";
    $result1 = mysqli_query($conn, $sql1);
    $numberofCustomers = mysqli_num_rows($result1);

    $sql2 = "SELECT SUM(total) AS total FROM expenses_details";
    $result2 = mysqli_query($conn, $sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'] ?? 0;

    $sql3 = "SELECT * FROM notifications WHERE status = 'Requested'";
    $result3 = mysqli_query($conn, $sql3);

    $sqlDrivers = "SELECT DISTINCT username FROM driver_details";
    $resultDrivers = mysqli_query($conn, $sqlDrivers);

    // Query for pending drivers
    $sql_pending = "SELECT id, name, email, username, phone FROM user_accounts WHERE role = 'driver' AND subscription_status = 'pending'";
    $result_pending = mysqli_query($conn, $sql_pending);
    $pending_count = mysqli_num_rows($result_pending);

    // Query for pending driver subscription receipts
    $sql_receipts = "SELECT ru.id, ru.user_id, ru.file_path, ru.upload_date, dd.name, dd.username, dd.subscription_type 
                     FROM receipt_uploads ru 
                     JOIN driver_details dd ON ru.user_id = dd.id 
                     WHERE ru.status = 'pending'";
    $result_receipts = mysqli_query($conn, $sql_receipts);
    $receipt_count = mysqli_num_rows($result_receipts);

    // Query for pending customer subscription receipts
    $sql_customer_receipts = "SELECT ru.id, ru.user_id, ru.file_path, ru.upload_date, ua.name, ua.email, ua.subscription_type 
                              FROM receipts_uploadC ru 
                              JOIN user_accounts ua ON ru.user_id = ua.id 
                              WHERE ru.status = 'pending' AND ua.role = 'customer'";
    $result_customer_receipts = mysqli_query($conn, $sql_customer_receipts);
    $customer_receipt_count = mysqli_num_rows($result_customer_receipts);

    // Handle existing notification actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle ride request notifications
        if (isset($_POST['accept']) || isset($_POST['decline'])) {
            $driverName = $_POST['drivername'] ?? 'None';
            $notificationId = $_POST['notificationId'];

            if (isset($_POST['accept'])) {
                if ($driverName === 'None') {
                    echo "<script>alert('Please select a driver first');</script>";
                } else {
                    $query = "UPDATE notifications 
                              SET status = 'Pending', 
                                  DriverId = (SELECT Id FROM driver_details WHERE username = ?) 
                              WHERE Id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('si', $driverName, $notificationId);
                    if ($stmt->execute()) {
                        echo "<script>alert('Notification updated successfully.');</script>";
                    } else {
                        echo "<script>alert('Failed to update notification.');</script>";
                    }
                    $stmt->close();
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } elseif (isset($_POST['decline'])) {
                $query = "UPDATE notifications SET status = 'Declined' WHERE Id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $notificationId);
                if ($stmt->execute()) {
                    echo "<script>alert('Notification declined successfully.');</script>";
                } else {
                    echo "<script>alert('Failed to decline notification.');</script>";
                }
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        // Handle driver approval
        if (isset($_POST['approve_driver'])) {
            $driver_id = $_POST['driver_id'];
            $conn->begin_transaction();
            $stmt_user = $conn->prepare("UPDATE user_accounts SET subscription_status = 'inactive' WHERE id = ? AND role = 'driver'");
            $stmt_user->bind_param("i", $driver_id);
            $user_success = $stmt_user->execute();

            $stmt_driver = $conn->prepare("UPDATE driver_details SET subscription_status = 'inactive' WHERE id = ?");
            $stmt_driver->bind_param("i", $driver_id);
            $driver_success = $stmt_driver->execute();

            if ($user_success && $driver_success) {
                $conn->commit();
                echo '<script>alert("Driver approved successfully."); window.location.href = "adminNotifications.php";</script>';
            } else {
                $conn->rollback();
                echo '<script>alert("Error approving driver."); window.location.href = "adminNotifications.php";</script>';
            }
            $stmt_user->close();
            $stmt_driver->close();
        }

        // Handle driver subscription approval/rejection
        if (isset($_POST['approve_subscription'])) {
            $receipt_id = $_POST['receipt_id'];
            $user_id = $_POST['user_id'];
            $subscription_type = $_POST['subscription_type'];

            // Debug: Log the subscription type being used
            error_log("Approving subscription for user_id: $user_id with subscription_type: $subscription_type");

            $conn->begin_transaction();
            try {
                // Update receipt_uploads status
                $stmt = $conn->prepare("UPDATE receipt_uploads SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $receipt_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in receipt_uploads for receipt_id: $receipt_id");
                }
                $stmt->close();

                // Update user_accounts
                $stmt = $conn->prepare("UPDATE user_accounts SET subscription_type = ?, subscription_status = 'active' WHERE id = ?");
                $stmt->bind_param("si", $subscription_type, $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in user_accounts for user_id: $user_id");
                }
                $stmt->close();

                // Update driver_details
                $stmt = $conn->prepare("UPDATE driver_details SET subscription_type = ?, subscription_status = 'active' WHERE id = ?");
                $stmt->bind_param("si", $subscription_type, $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in driver_details for user_id: $user_id");
                }
                $stmt->close();

                $conn->commit();
                echo '<script>alert("Subscription approved successfully."); window.location.href = "adminNotifications.php";</script>';
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error approving subscription: " . $e->getMessage());
                echo '<script>alert("Error approving subscription: ' . addslashes($e->getMessage()) . '"); window.location.href = "adminNotifications.php";</script>';
            }
        }

        if (isset($_POST['reject_subscription'])) {
            $receipt_id = $_POST['receipt_id'];
            $user_id = $_POST['user_id'];

            $conn->begin_transaction();
            try {
                // Update receipt_uploads status
                $stmt = $conn->prepare("UPDATE receipt_uploads SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $receipt_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in receipt_uploads for receipt_id: $receipt_id");
                }
                $stmt->close();

                // Revert user_accounts to basic/inactive
                $stmt = $conn->prepare("UPDATE user_accounts SET subscription_type = 'basic', subscription_status = 'inactive' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in user_accounts for user_id: $user_id");
                }
                $stmt->close();

                // Revert driver_details to basic/inactive
                $stmt = $conn->prepare("UPDATE driver_details SET subscription_type = 'basic', subscription_status = 'inactive' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in driver_details for user_id: $user_id");
                }
                $stmt->close();

                $conn->commit();
                echo '<script>alert("Subscription rejected successfully."); window.location.href = "adminNotifications.php";</script>';
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error rejecting subscription: " . $e->getMessage());
                echo '<script>alert("Error rejecting subscription: ' . addslashes($e->getMessage()) . '"); window.location.href = "adminNotifications.php";</script>';
            }
        }

        // Handle customer subscription approval/rejection
        if (isset($_POST['approve_customer_subscription'])) {
            $receipt_id = $_POST['receipt_id'];
            $user_id = $_POST['user_id'];
            $subscription_type = $_POST['subscription_type'];

            // Debug: Log the subscription type being used
            error_log("Approving customer subscription for user_id: $user_id with subscription_type: $subscription_type");

            $conn->begin_transaction();
            try {
                // Update receipts_uploadC status
                $stmt = $conn->prepare("UPDATE receipts_uploadC SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $receipt_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in receipts_uploadC for receipt_id: $receipt_id");
                }
                $stmt->close();

                // Update user_accounts
                $stmt = $conn->prepare("UPDATE user_accounts SET subscription_type = ?, subscription_status = 'active' WHERE id = ? AND role = 'customer'");
                $stmt->bind_param("si", $subscription_type, $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in user_accounts for user_id: $user_id");
                }
                $stmt->close();

                $conn->commit();
                echo '<script>alert("Customer subscription approved successfully."); window.location.href = "adminNotifications.php";</script>';
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error approving customer subscription: " . $e->getMessage());
                echo '<script>alert("Error approving customer subscription: ' . addslashes($e->getMessage()) . '"); window.location.href = "adminNotifications.php";</script>';
            }
        }

        if (isset($_POST['reject_customer_subscription'])) {
            $receipt_id = $_POST['receipt_id'];
            $user_id = $_POST['user_id'];

            $conn->begin_transaction();
            try {
                // Update receipts_uploadC status
                $stmt = $conn->prepare("UPDATE receipts_uploadC SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $receipt_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in receipts_uploadC for receipt_id: $receipt_id");
                }
                $stmt->close();

                // Revert user_accounts to basic/inactive
                $stmt = $conn->prepare("UPDATE user_accounts SET subscription_type = 'basic', subscription_status = 'inactive' WHERE id = ? AND role = 'customer'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    throw new Exception("No rows updated in user_accounts for user_id: $user_id");
                }
                $stmt->close();

                $conn->commit();
                echo '<script>alert("Customer subscription rejected successfully."); window.location.href = "adminNotifications.php";</script>';
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error rejecting customer subscription: " . $e->getMessage());
                echo '<script>alert("Error rejecting customer subscription: ' . addslashes($e->getMessage()) . '"); window.location.href = "adminNotifications.php";</script>';
            }
        }
    }

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    }
} else {
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <?php include 'cardStyles.php'; ?>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-color: #1A202C; color: #E2E8F0; min-height: 100vh; overflow-x: hidden; }
        .grid-container { display: flex; width: 100%; position: relative; }
        .header { background-color: #2D3748; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .menu-icon { cursor: pointer; color: #7F9CF5; font-size: 24px; }
        .header-right label { font-size: 1.1rem; color: #CBD5E0; }
        #sidebar { width: 250px; min-width: 250px; background-color: #2D3748; padding-top: 80px; position: fixed; top: 0; left: 0; height: 100%; z-index: 999; transition: transform 0.3s ease; }
        #sidebar.closed { transform: translateX(-250px); }
        .main-container { width: calc(100% - 250px); padding: 100px 40px 40px; background-color: #1A202C; margin-left: 250px; min-height: calc(100vh - 60px); }
        .main-title { text-align: center; margin-bottom: 30px; }
        .main-title h2 { font-size: 2.5rem; font-weight: 700; color: #7F9CF5; text-transform: uppercase; letter-spacing: 1px; animation: slideIn 0.5s ease; }
        .mainCards { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; margin-bottom: 40px; }
        .dashboard-card { flex: 1; min-width: 200px; background-color: #2D3748; border-radius: 10px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; animation: fadeIn 1s ease; text-decoration: none; color: #E2E8F0; position: relative; overflow: hidden; }
        .dashboard-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); }
        .dashboard-card .card-icon { font-size: 2.5rem; color: #7F9CF5; margin-bottom: 10px; }
        .dashboard-card h3 { font-size: 1.1rem; color: #CBD5E0; margin-bottom: 10px; }
        .dashboard-card .value { font-size: 1.5rem; font-weight: 600; color: #fff; }
        .dashboard-card .card-popup { position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 10px 0; font-size: 16px; text-align: center; opacity: 0; transition: opacity 0.3s; }
        .dashboard-card:hover .card-popup { opacity: 1; }
        .card-section { background: #2D3748; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); animation: fadeIn 0.8s ease; }
        .card-section h3 { font-size: 1.5rem; font-weight: 600; color: #7F9CF5; margin-bottom: 20px; }
        .notification-count { background: #E53E3E; color: white; border-radius: 50%; padding: 4px 8px; font-size: 14px; margin-left: 10px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; background: #2D3748; border-radius: 12px; overflow: hidden; animation: fadeIn 0.8s ease; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #4A5568; color: #E2E8F0; }
        th { background: #805AD5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.9rem; }
        tr:hover td { background: #4A5568; }
        .approve-btn, .reject-btn { padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.3s ease; }
        .approve-btn { background: linear-gradient(90deg, #4A90E2, #63B3ED); color: white; }
        .approve-btn:hover { background: linear-gradient(90deg, #357ABD, #4EC0E6); }
        .reject-btn { background: linear-gradient(90deg, #E53E3E, #FC8181); color: white; }
        .reject-btn:hover { background: linear-gradient(90deg, #C53030, #F56565); }
        .reportTable ul { list-style: none; text-align: right; margin-bottom: 15px; }
        .reportTable a { color: #7F9CF5; text-decoration: none; font-size: 1rem; padding: 8px 15px; border-radius: 6px; transition: background 0.3s ease; }
        .reportTable a:hover { background: #7F9CF5; color: white; }
        .reportTable a i { margin-right: 5px; }
        a[href^="http"] { color: #7F9CF5; text-decoration: none; transition: color 0.3s ease; }
        a[href^="http"]:hover { color: #63B3ED; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @media (max-width: 1024px) { 
            .main-container { padding: 90px 20px 20px; margin-left: 200px; width: calc(100% - 200px); } 
            .dashboard-card { min-width: 180px; }
        }
        @media (max-width: 768px) { 
            .grid-container { flex-direction: column; } 
            #sidebar { width: 100%; min-width: 100%; padding-top: 70px; position: relative; transform: none; } 
            #sidebar.closed { transform: none; display: none; } 
            .main-container { padding: 80px 15px 15px; margin-left: 0; width: 100%; } 
            .main-title h2 { font-size: 2rem; } 
            .dashboard-card { min-width: 100%; } 
            table { font-size: 0.85rem; } 
            th, td { padding: 10px; } 
        }
        @media (max-width: 480px) { 
            .main-title h2 { font-size: 1.5rem; } 
            .card-section { padding: 15px; } 
            th, td { padding: 8px; font-size: 0.8rem; } 
            .approve-btn, .reject-btn { padding: 6px 12px; font-size: 0.9rem; } 
        }
    </style>
</head>

<body>
    <div class="grid-container">
        <?php include 'adminSideBar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Notifications</h2>
            </div>

            <!-- Added Cards Section -->
            <div class="mainCards">
                <a href="view_drivers.php" class="dashboard-card">
                    <div class="card-icon">🚗</div>
                    <h3>Number of Drivers</h3>
                    <div class="value"><?php echo $numberofDrivers; ?></div>
                    <div class="card-popup">View Drivers</div>
                </a>
                <a href="view_users.php" class="dashboard-card">
                    <div class="card-icon">👤</div>
                    <h3>Number of Customers</h3>
                    <div class="value"><?php echo $numberofCustomers; ?></div>
                    <div class="card-popup">View Users</div>
                </a>
                <a href="admin_expense_report.php" class="dashboard-card">
                    <div class="card-icon">💰</div>
                    <h3>Expenses</h3>
                    <div class="value"><?php echo $totalExpenses; ?></div>
                    <div class="card-popup">View Expenses</div>
                </a>
            </div>

            <!-- Existing Notifications Table -->
            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                    <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i> Print
                        </a>
                    </li>
                </ul>
                <div class="card-section">
                    <table>
                        <thead>
                            <tr>
                                <th>Notification</th>
                                <th>From</th>
                                <th>Status</th>
                                <th>Driver</th>
                                <th>Action</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result3)) { 
                                mysqli_data_seek($resultDrivers, 0); ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <form id="filterForm" method="POST">
                                            <select id="drivername" name="drivername" style="padding: 5px; border-radius: 6px; border: 1px solid #4A5568; background: #1A202C; color: #E2E8F0;">
                                                <option value="None">None</option>
                                                <?php while ($driver = mysqli_fetch_assoc($resultDrivers)) { ?>
                                                    <option value="<?php echo $driver['username']; ?>"><?php echo htmlspecialchars($driver['username']); ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="notificationId" value="<?php echo $row['id']; ?>">
                                    </td>
                                    <td>
                                        <button type="submit" name="accept" class="approve-btn">Accept</button>
                                    </td>
                                    <td>
                                        <button type="submit" name="decline" class="reject-btn">Decline</button>
                                    </td>
                                        </form>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Driver Approvals Section -->
            <div class="card-section">
                <h3>Pending Driver Approvals <span class="notification-count"><?php echo $pending_count; ?></span></h3>
                <?php if ($pending_count > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($driver = mysqli_fetch_assoc($result_pending)) { ?>
                                <tr>
                                    <td><?php echo $driver['id']; ?></td>
                                    <td><?php echo htmlspecialchars($driver['name']); ?></td>
                                    <td><?php echo htmlspecialchars($driver['email']); ?></td>
                                    <td><?php echo htmlspecialchars($driver['username']); ?></td>
                                    <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                                    <td>
                                        <form method="POST" action="adminNotifications.php">
                                            <input type="hidden" name="driver_id" value="<?php echo $driver['id']; ?>">
                                            <button type="submit" name="approve_driver" class="approve-btn">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p style="text-align: center; color: #A0AEC0;">No pending driver approvals at this time.</p>
                <?php } ?>
            </div>

            <!-- Pending Driver Subscription Approvals Section -->
            <div class="card-section">
                <h3>Pending Subscription Approvals <span class="notification-count"><?php echo $receipt_count; ?></span></h3>
                <?php if ($receipt_count > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Plan</th>
                                <th>Receipt</th>
                                <th>Submitted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($receipt = mysqli_fetch_assoc($result_receipts)) { ?>
                                <tr>
                                    <td><?php echo $receipt['id']; ?></td>
                                    <td><?php echo htmlspecialchars($receipt['name']); ?></td>
                                    <td><?php echo htmlspecialchars($receipt['username']); ?></td>
                                    <td><?php echo ucfirst($receipt['subscription_type']); ?></td>
                                    <td><a href="<?php echo $receipt['file_path']; ?>" target="_blank" style="color: #7F9CF5;">View</a></td>
                                    <td><?php echo $receipt['upload_date']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="receipt_id" value="<?php echo $receipt['id']; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $receipt['user_id']; ?>">
                                            <input type="hidden" name="subscription_type" value="<?php echo $receipt['subscription_type']; ?>">
                                            <button type="submit" name="approve_subscription" class="approve-btn">Approve</button>
                                            <button type="submit" name="reject_subscription" class="reject-btn">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p style="text-align: center; color: #A0AEC0;">No pending subscription approvals at this time.</p>
                <?php } ?>
            </div>

            <!-- Pending Customer Subscription Approvals Section -->
            <div class="card-section">
                <h3>Pending Customer Subscription Approvals <span class="notification-count"><?php echo $customer_receipt_count; ?></span></h3>
                <?php if ($customer_receipt_count > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Plan</th>
                                <th>Receipt</th>
                                <th>Submitted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($receipt = mysqli_fetch_assoc($result_customer_receipts)) { ?>
                                <tr>
                                    <td><?php echo $receipt['id']; ?></td>
                                    <td><?php echo htmlspecialchars($receipt['name']); ?></td>
                                    <td><?php echo htmlspecialchars($receipt['email']); ?></td>
                                    <td><?php echo ucfirst($receipt['subscription_type']); ?></td>
                                    <td><a href="<?php echo $receipt['file_path']; ?>" target="_blank" style="color: #7F9CF5;">View</a></td>
                                    <td><?php echo $receipt['upload_date']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="receipt_id" value="<?php echo $receipt['id']; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $receipt['user_id']; ?>">
                                            <input type="hidden" name="subscription_type" value="<?php echo $receipt['subscription_type']; ?>">
                                            <button type="submit" name="approve_customer_subscription" class="approve-btn">Approve</button>
                                            <button type="submit" name="reject_customer_subscription" class="reject-btn">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p style="text-align: center; color: #A0AEC0;">No pending customer subscription approvals at this time.</p>
                <?php } ?>
            </div>
        </main>
    </div>

    <script src="myScript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="dashboardscript.js"></script>
    <script>
        function printTable() {
            var divToPrint = document.getElementById('ReportToBePrinted').getElementsByTagName('table')[0].outerHTML;
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html><head><title>Print</title><style>table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #805AD6; color: white;}</style></head><body>' + divToPrint + '</body></html>');
            newWin.document.close();
            newWin.print();
            setTimeout(function () { newWin.close(); }, 10);
        }
    </script>
</body>
</html>