<?php
include('connect.php');
session_start();

if ($_SESSION['loggedin'] == true) {
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

    $columnQuery = "SELECT mode, COUNT(*) AS total FROM booking_detail GROUP BY mode";
    $columnResult = mysqli_query($conn, $columnQuery);
    $columnData = array();
    while ($row = mysqli_fetch_assoc($columnResult)) {
        $columnData[] = array("label" => $row['mode'], "y" => $row['total']);
    }

    $pieQuery = "SELECT mode, SUM(CAST(payment AS DECIMAL(10,2))) AS total_payment FROM booking_detail GROUP BY mode";
    $pieResult = mysqli_query($conn, $pieQuery);
    $pieData = array();
    while ($row = mysqli_fetch_assoc($pieResult)) {
        $pieData[] = array("label" => $row['mode'], "y" => $row['total_payment']);
    }

    $mySql1 = "SELECT COUNT(*) trips FROM booking_detail";
    $noOfTrips1 = mysqli_query($conn, $mySql1);
    $row1 = mysqli_fetch_assoc($noOfTrips1);
    $noOfTrips = $row1['trips'];

    $mySql2 = "SELECT SUM(payment) payments FROM booking_detail";
    $totalSales1 = mysqli_query($conn, $mySql2);
    $row2 = mysqli_fetch_assoc($totalSales1);
    $totalSales = $row2['payments'] ?? 0;

    $net = 'Network';
    $mySql3 = "SELECT SUM(payment) netPay FROM booking_detail WHERE mode = '$net'";
    $networkSales1 = mysqli_query($conn, $mySql3);
    $row3 = mysqli_fetch_assoc($networkSales1);
    $networkSales = $row3['netPay'] ?? 0;

    $mySql4 = "SELECT SUM(payment) - (SELECT SUM(total) AS balance FROM expenses_details) AS balance FROM booking_detail";
    $balance1 = mysqli_query($conn, $mySql4);
    $row4 = mysqli_fetch_assoc($balance1);
    $balance = $row4['balance'] ?? 0;

    $mySql5 = "SELECT COUNT(*) noOfStaff FROM staff_details";
    $noOfStaff1 = mysqli_query($conn, $mySql5);
    $row5 = mysqli_fetch_assoc($noOfStaff1);
    $noOfStaff = $row5['noOfStaff'];

    $mySql6 = "SELECT COUNT(*) noOfVehicles FROM vehicle_details";
    $noOfVehicles1 = mysqli_query($conn, $mySql6);
    $row6 = mysqli_fetch_assoc($noOfVehicles1);
    $noOfVehicles = $row6['noOfVehicles'];

    $sql3 = "SELECT * FROM booking_detail WHERE ride_status = 'Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);

    $sql4 = "SELECT * FROM booking_detail WHERE ride_status != 'Completed'";
    $result4 = mysqli_query($conn, $sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);

    // Query for pending count (for sidebar badge only)
    $sql_pending = "SELECT COUNT(*) AS pending FROM user_accounts WHERE role = 'driver' AND subscription_status = 'pending'";
    $result_pending_count = mysqli_query($conn, $sql_pending);
    $pending_row = mysqli_fetch_assoc($result_pending_count);
    $pending_count = $pending_row['pending'];

    $completeArray = array();
    $completeArray[0]["label"] = 'Customer';
    $completeArray[0]["y"] = $numberofCustomers;
    $completeArray[1]["label"] = 'Driver';
    $completeArray[1]["y"] = $numberofDrivers;
    $completeArray[2]["label"] = 'Rides Completed';
    $completeArray[2]["y"] = $numOfCompletedRides;
    $completeArray[3]["label"] = 'Incomplete Rides';
    $completeArray[3]["y"] = $numOfIncompleteRides;

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.php");
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
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/dashboardstyle.css">

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
        .main-title h2 { font-size: 2.5rem; font-weight: 700; color: #7F9CF5; text-align: center; margin-bottom: 30px; animation: slideIn 0.5s ease; text-transform: uppercase; letter-spacing: 1px; }
        .mainCards { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; margin-bottom: 40px; }
        .dashboard-card { flex: 1; min-width: 200px; background-color: #2D3748; border-radius: 10px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; animation: fadeIn 1s ease; text-decoration: none; color: #E2E8F0; position: relative; overflow: hidden; }
        .dashboard-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); }
        .dashboard-card .card-icon { font-size: 2.5rem; color: #7F9CF5; margin-bottom: 10px; }
        .dashboard-card h3 { font-size: 1.1rem; color: #CBD5E0; margin-bottom: 10px; }
        .dashboard-card .value { font-size: 1.5rem; font-weight: 600; color: #fff; }
        .dashboard-card .card-popup { position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 10px 0; font-size: 16px; text-align: center; opacity: 0; transition: opacity 0.3s; }
        .dashboard-card:hover .card-popup { opacity: 1; }
        .chart-title { font-size: 1.5rem; font-weight: 600; color: #7F9CF5; margin-bottom: 20px; text-align: center; }
        .sidebar-section { padding: 12px 20px; margin: 8px 0; font-size: 1.1rem; cursor: pointer; color: #fff; background: #805AD5; border-radius: 8px; transition: all 0.3s ease; }
        .sidebar-section:hover { background: #6B46C1; }
        .sidebar-sublist { display: none; padding-left: 20px; }
        .sidebar-list-item a { color: #CBD5E0; text-decoration: none; transition: color 0.3s ease; }
        .sidebar-list-item a:hover { color: #7F9CF5; }
        .notification-count { background: #E53E3E; color: white; border-radius: 50%; padding: 4px 8px; font-size: 14px; margin-left: 10px; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @media (max-width: 1024px) { .main-container { padding: 90px 20px 20px; margin-left: 200px; width: calc(100% - 200px); } }
        @media (max-width: 768px) { .grid-container { flex-direction: column; } #sidebar { width: 100%; min-width: 100%; padding-top: 70px; position: relative; transform: none; } #sidebar.closed { transform: none; display: none; } .main-container { padding: 80px 15px 15px; margin-left: 0; width: 100%; } .main-title h2 { font-size: 2rem; } .dashboard-card { min-width: 150px; } }
        @media (max-width: 480px) { .main-title h2 { font-size: 1.5rem; } .dashboard-card { min-width: 120px; padding: 15px; } .chart-title { font-size: 1.2rem; } }
    </style>

    <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1",
                title: { text: "" },
                axisY: { includeZero: true },
                data: [{
                    type: "column",
                    indexLabelFontColor: "#5A5757",
                    indexLabelPlacement: "outside",
                    dataPoints: <?php echo json_encode($completeArray, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

            var chart = new CanvasJS.Chart("chartContainer2", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1",
                title: { text: "Payment Mode Analysis" },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{label}",
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - #percent%",
                    yValueFormatString: "฿#,##0",
                    dataPoints: <?php echo json_encode($pieData, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        }
    </script>
</head>

<body>
    <div class="grid-container">
        <header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <label style="margin-left: 35px;"><?php echo $_SESSION['name']; ?></label>
            </div>
        </header>

        <aside id="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-brand">Direct Drive</div>
                <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
            </div>
            <ul class="sidebar-list">
                <li class="sidebar-list-item">
                    <a href="dashboard.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="adminNotifications.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Notifications<?php if ($pending_count > 0) { echo "<span class='notification-count'>$pending_count</span>"; } ?></span>
                    </a>
                </li>
                <li class="sidebar-section" onclick="toggleSection('add-section')">
                    <i class='bx bxs-plus-circle'></i>
                    <span class="links_name">ADD</span>
                </li>
                <ul id="add-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="add-driver.php"><i class='bx bxs-user'></i><span class="links_name">Add Drivers</span></a></li>
                    <li class="sidebar-list-item"><a href="assignDriverVehicle.php"><i class='bx bxs-user'></i><span class="links_name">Assign Vehicle</span></a></li>
                    <li class="sidebar-list-item"><a href="addCustomer.php"><i class='bx bxs-user'></i><span class="links_name">Add Customer</span></a></li>
                    <li class="sidebar-list-item"><a href="ad-vehicle.php"><i class='bx bxs-car'></i><span class="links_name">Add Vehicles</span></a></li>
                    <?php if ($_SESSION['role'] != 'Staff'): ?>
                    <li class="sidebar-list-item"><a href="add-staff.php"><i class='bx bxs-user'></i><span class="links_name">Add Staff</span></a></li>
                    <?php endif; ?>
                    <li class="sidebar-list-item"><a href="add-expenses.php"><i class='bx bxs-credit-card'></i><span class="links_name">Add Expenses</span></a></li>
                </ul>
                <li class="sidebar-section" onclick="toggleSection('view-section')">
                    <i class='bx bxs-show'></i>
                    <span class="links_name">VIEW</span>
                </li>
                <ul id="view-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="view_users.php"><i class='bx bxs-user'></i><span class="links_name">View Users</span></a></li>
                    <li class="sidebar-list-item"><a href="view_drivers.php"><i class='bx bxs-user'></i><span class="links_name">View Drivers</span></a></li>
                    <li class="sidebar-list-item"><a href="view_staff.php"><i class='bx bxs-user'></i><span class="links_name">View Staff</span></a></li>
                    <li class="sidebar-list-item"><a href="view_vehicles.php"><i class='bx bxs-car'></i><span class="links_name">View Vehicles</span></a></li>
                </ul>
                <li class="sidebar-section" onclick="toggleSection('report-section')">
                    <i class='bx bxs-report'></i>
                    <span class="links_name">REPORT</span>
                </li>
                <ul id="report-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="driverDetails.php"><i class='bx bxs-report'></i><span class="links_name">Trips</span></a></li>
                    <li class="sidebar-list-item"><a href="monthReports.php"><i class='bx bxs-report'></i><span class="links_name">Monthly Report</span></a></li>
                    <li class="sidebar-list-item"><a href="downloadPictures.php"><i class='bx bxs-report'></i><span class="links_name">Download Pictures</span></a></li>
                    <li class="sidebar-list-item"><a href="admin_income_repor.php"><i class='bx bxs-report'></i><span class="links_name">Income Report</span></a></li>
                    <li class="sidebar-list-item"><a href="admin_expense_report.php"><i class='bx bxs-report'></i><span class="links_name">Expense Report</span></a></li>
                    <li class="sidebar-list-item"><a href="adminDeposits.php"><i class='bx bxs-report'></i><span class="links_name">Deposit Report</span></a></li>
                    <li class="sidebar-list-item"><a href="reports.php"><i class='bx bxs-report'></i><span class="links_name">Client Report</span></a></li>
                </ul>
                <li class="sidebar-section" onclick="toggleSection('cash-section')">
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="links_name">CASH</span>
                </li>
                <ul id="cash-section" class="sidebar-sublist">
                    <li class="sidebar-list-item"><a href="update_office_cash.php"><i class='bx bxs-user'></i><span class="links_name">Update Office Cash</span></a></li>
                    <li class="sidebar-list-item"><a href="add-expenses.php"><i class='bx bxs-credit-card'></i><span class="links_name">Add Expenses</span></a></li>
                </ul>
                <li class="sidebar-list-item">
                    <a href="index.php?logout"><i class='bx bx-log-out'></i><span class="links_name">Logout</span></a>
                </li>
            </ul>
        </aside>

        <main class="main-container">
            <div class="main-title">
                <h2>Dashboard</h2>
            </div>

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
                <a href="expenceDetails.php" class="dashboard-card">
                    <div class="card-icon">📈</div>
                    <h3>Total Sales</h3>
                    <div class="value"><?php echo $totalSales; ?></div>
                    <div class="card-popup">View Sales</div>
                </a>
                <a href="driverDetails.php" class="dashboard-card">
                    <div class="card-icon">📊</div>
                    <h3>Network Sales</h3>
                    <div class="value"><?php echo $networkSales; ?></div>
                    <div class="card-popup">View Network Sales</div>
                </a>
                <a href="driverDetails.php" class="dashboard-card">
                    <div class="card-icon">📍</div>
                    <h3>Number of Trips</h3>
                    <div class="value"><?php echo $noOfTrips; ?></div>
                    <div class="card-popup">View Trips</div>
                </a>
                <a href="expenceDetails.php" class="dashboard-card">
                    <div class="card-icon">💳</div>
                    <h3>Balance</h3>
                    <div class="value"><?php echo $balance; ?></div>
                    <div class="card-popup">View Balance</div>
                </a>
                <a href="view_staff.php" class="dashboard-card">
                    <div class="card-icon">💻</div>
                    <h3>Number of Staff</h3>
                    <div class="value"><?php echo $noOfStaff; ?></div>
                    <div class="card-popup">View Staff</div>
                </a>
                <a href="view_vehicles.php" class="dashboard-card">
                    <div class="card-icon">🚕</div>
                    <h3>Number of Vehicles</h3>
                    <div class="value"><?php echo $noOfVehicles; ?></div>
                    <div class="card-popup">View Vehicles</div>
                </a>
            </div>

            <h2 class="chart-title">Rides Data</h2>
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <h2 class="chart-title">Payment Data</h2>
            <div id="chartContainer2" style="height: 370px; width: 100%;"></div>
        </main>
    </div>

    <script src="myScript.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
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