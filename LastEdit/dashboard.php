<?php
include('connect.php');
session_start();
if($_SESSION['loggedin']==true){
    $sql0="select * from driver_details where role ='Driver'";
    $result0 = mysqli_query($conn,$sql0);
    $numberofDrivers = mysqli_num_rows($result0);
    $sql1="select * from user_accounts where role ='customer'";
    $result1 = mysqli_query($conn,$sql1);
    $numberofCustomers = mysqli_num_rows($result1);
    $sql2="SELECT SUM(total) AS total FROM expenses_details;";
    $result2 = mysqli_query($conn,$sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];

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
    $mySql1="SELECT COUNT(*) trips FROM booking_detail;";
    $noOfTrips1 = mysqli_query($conn,$mySql1);
    $row1 = mysqli_fetch_assoc($noOfTrips1);
    $noOfTrips = $row1['trips'];


    $mySql2="SELECT SUM(payment) payments FROM booking_detail;";
    $totalSales1 = mysqli_query($conn,$mySql2);
    $row2 = mysqli_fetch_assoc($totalSales1);
    $totalSales = $row2['payments'];

    $net = 'Network';
    $mySql3="SELECT SUM(payment) netPay FROM booking_detail WHERE mode = '$net';";
    $networkSales1 = mysqli_query($conn,$mySql3);
    $row3 = mysqli_fetch_assoc($networkSales1);
    $networkSales = $row3['netPay'];

    $mySql4="SELECT SUM(payment) - (Select SUM(total) as balance FROM expenses_details) as balance FROM booking_detail;";
    $balance1 = mysqli_query($conn,$mySql4);
    $row4 = mysqli_fetch_assoc($balance1);
    $balance = $row4['balance'];

    $mySql5="SELECT COUNT(*) noOfStaff FROM staff_details;";
    $noOfStaff1 = mysqli_query($conn,$mySql5);
    $row5 = mysqli_fetch_assoc($noOfStaff1);
    $noOfStaff = $row5['noOfStaff'];

    $mySql6="SELECT COUNT(*) noOfVehicles FROM vehicle_details;";
    $noOfVehicles1 = mysqli_query($conn,$mySql6);
    $row6 = mysqli_fetch_assoc($noOfVehicles1);
    $noOfVehicles = $row6['noOfVehicles'];


     

    $sql3 ="select * from booking_detail where  ride_status ='Completed'";
    $result3 = mysqli_query($conn,$sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);
    $sql4 ="select * from booking_detail where ride_status !='Completed'";
    $result4 = mysqli_query($conn,$sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);
    $completeArray = array();
$count = 0;
$completeArray[0]["label"]='Customer';
$completeArray[0]["y"]=$numberofCustomers;
$completeArray[1]["label"]='Driver';
$completeArray[1]["y"]=$numberofDrivers;
$completeArray[2]["label"]='Rides Completed';
$completeArray[2]["y"]=$numOfCompletedRides;
$completeArray[3]["label"]='Incomplete Rides';
$completeArray[3]["y"]=$numOfIncompleteRides;
    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }
}else{
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Montserrat Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <script>

</script>

<script>
    window.onload = function () {
        var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title:{
		text: ""
	},
	axisY:{
		includeZero: true
	},
	data: [{
		type: "column", //change type to bar, line, area, pie, etc
		//indexLabel: "{y}", //Shows y value on all Data Points
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
    title: {
        text: "Payment Mode Analysis"
    },
    data: [
        {
            type: "pie",  // Set the chart type to pie
            showInLegend: true,
            legendText: "{label}",
            indexLabelFontSize: 16,
            indexLabel: "{label} - #percent%",  // Display the label and percentage on the chart
            yValueFormatString: "฿#,##0",  // Format the value as currency
            dataPoints: <?php echo json_encode($pieData, JSON_NUMERIC_CHECK); ?>
        }
    ]
});
chart.render();
}

</script>

<style>
        .mainCards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .materialIconsOutlined {
            vertical-align: middle;
            line-height: 1px;
            font-size: 35px;
        }
        
        .myCard {
            width: 25%; /* Adjusted width for 4 cards in a row */
            height: 100px;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            text-decoration: none;
            color: black;
            position: relative;
            overflow: hidden;
            background-color: #1abc9c; /* Same color for all cards */
        }
        
        .cardInner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .cardInner > .materialIconsOutlined {
            font-size: 45px;
        }
        
        .card h2 {
            margin: 20px 0;
        }
        
        .card-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        
        .card-popup {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 10px 0;
            font-size: 16px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .myCard:hover .card-popup {
            opacity: 1;
        }
        
        .myCard:hover {
            opacity: 0.8;
            transform: translateY(-5px);
        }
    </style>

<style>
        /* Inline CSS for quick styling, move to styles.css if needed */
        .sidebar-section {
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
            color: black;
            background-color: #1abc9c;
            border-radius: 5px;
        }
        .sidebar-section:hover {
            background-color: #e0e0e0;
        }
        .sidebar-sublist {
            display: none; /* Initially hide all sections */
            padding-left: 20px;
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
        <!-- End Header -->

        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-brand">
                    Concord Transport
                </div>
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
                        <span class="links_name">Notifications</span>
                    </a>
                </li>
                
                <!-- ADD Section -->
                <li class="sidebar-section" onclick="toggleSection('add-section')">
                    <i class='bx bxs-plus-circle'></i>
                    <span class="links_name">ADD</span>
                </li>
                <ul id="add-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="add-driver.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Drivers</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="assignDriverVehicle.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Assign Vehicle</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="addCustomer.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Customer</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="ad-vehicle.php">
                            <i class='bx bxs-car'></i>
                            <span class="links_name">Add Vehicles</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['role'] != 'Staff'): ?>
                    <li class="sidebar-list-item">
                        <a href="add-staff.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Staff</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="sidebar-list-item">
                        <a href="add-expenses.php">
                            <i class='bx bxs-credit-card'></i>
                            <span class="links_name">Add Expenses</span>
                        </a>
                    </li>
                </ul>

                <!-- VIEW Section -->
                <li class="sidebar-section" onclick="toggleSection('view-section')">
                    <i class='bx bxs-show'></i>
                    <span class="links_name">VIEW</span>
                </li>
                <ul id="view-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="view_users.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Users</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_drivers.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Drivers</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_staff.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Staff</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_vehicles.php">
                            <i class='bx bxs-car'></i>
                            <span class="links_name">View Vehicles</span>
                        </a>
                    </li>
                </ul>

                <!-- REPORT Section -->
                <li class="sidebar-section" onclick="toggleSection('report-section')">
                    <i class='bx bxs-report'></i>
                    <span class="links_name">REPORT</span>
                </li>
                <ul id="report-section" class="sidebar-sublist">
                <li class="sidebar-list-item">
                        <a href="driverDetails.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Trips</span>
                        </a>
                    </li>

                    <li class="sidebar-list-item">
                        <a href="monthReports.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Monthly Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="downloadPictures.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Download Pictures</span> 
                        </a>
                    </li>

                    <li class="sidebar-list-item">
                        <a href="admin_income_repor.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Client Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="admin_expense_report.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Expense Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="adminDeposits.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Deposit Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="reports.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Client Report</span>
                        </a>
                    </li>
                   
                    
                </ul>

                <!-- CASH Section -->
                <li class="sidebar-section" onclick="toggleSection('cash-section')">
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="links_name">CASH</span>
                </li>
                <ul id="cash-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="update_office_cash.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Update Office Cash</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="add-expenses.php">
                            <i class='bx bxs-credit-card'></i>
                            <span class="links_name">Add Expenses</span>
                        </a>
                    </li>
                   
                </ul>

                <li class="sidebar-list-item">
                    <a href="view_users.php?logout">
                        <i class='bx bx-log-out'></i>
                        <span class="links_name">Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- End Sidebar -->

        <!-- Main -->
        <main class="main-container">
        <div class="main-title">
            <h2>DASHBOARD</h2>
        </div>

        <div class="mainCards">


        <a href="view_drivers.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h3>Number of Drivers</h3>
                    <h2><?php echo $numberofDrivers; ?></h2>
                </div>
                <div class="card-popup">View Drivers</div>
            </a>

            <a href="view_users.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128100;</div>
                    <h3>Number of Customers</h3>
                    <h2><?php echo $numberofCustomers; ?></h2>
                </div>
                <div class="card-popup">View Users</div>
            </a>

            <a href="admin_expense_report.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128176;</div>
                    <h3>Expenses</h3>
                    <h2><?php echo $totalExpenses; ?></h2>
                </div>
                <div class="card-popup">View Expenses</div>
            </a>

            <a href="expenceDetails.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128200;</div>
                    <h3>Total Sales</h3>
                    <h2><?php echo $totalSales; ?></h2>
                </div>
                <div class="card-popup">View Sales</div>
            </a>

            <a href="driverDetails.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128202;</div>
                    <h3>Network Sales</h3>
                    <h2><?php echo $networkSales; ?></h2>
                </div>
                <div class="card-popup">View Network Sales</div>
            </a>

            <a href="driverDetails.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128205;</div>
                    <h3>Number of Trips</h3>
                    <h2><?php echo $noOfTrips; ?></h2>
                </div>
                <div class="card-popup">View Trips</div>
            </a>

          

            <a href="expenceDetails.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128179;</div>
                    <h3>Balance</h3>
                    <h2><?php echo $balance; ?></h2>
                </div>
                <div class="card-popup">View Balance</div>
            </a>

            

            <a href="view_staff.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128187;</div>
                    <h3>Number of Staff</h3>
                    <h2><?php echo $noOfStaff; ?></h2>
                </div>
                <div class="card-popup">View Staff</div>
            </a>

            <a href="view_vehicles.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128661;</div>
                    <h3>Number of Vehicles</h3>
                    <h2><?php echo $noOfVehicles; ?></h2>
                </div>
                <div class="card-popup">View Vehicles</div>
            </a>
        </div>

        <h2 class="chart-title">Rides Data</h2>
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <h2 class="chart-title">Payment Data</h2>
        <div id="chartContainer2" style="height: 370px; width: 100%;"></div>
    </main>
        <!-- End Main -->
    </div>
    <!-- Scripts -->
    


<script src="myScript.js"></script> <!-- Link to your JavaScript file -->
    <!-- ApexCharts -->
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
     
</body>

</html>