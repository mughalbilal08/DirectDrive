<?php
include('connect.php');
session_start();

if ($_SESSION['loggedin'] == true) {
    $did = $_SESSION['id'];

    $sql10 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status = 'Completed'";
    $result10 = mysqli_query($conn, $sql10);
    $numOfCompletedRides1 = mysqli_num_rows($result10);

    $sql11 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status != 'Completed'";
    $result11 = mysqli_query($conn, $sql11);
    $numOfIncompleteRides1 = mysqli_num_rows($result11);

    // Query to fetch completed rides
    $sql3 = "SELECT * FROM notifications  WHERE DriverId = '$did' AND status = 'Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides2 = mysqli_num_rows($result3);

    // Query to fetch incomplete rides
    $sql1 = "SELECT * FROM notifications  WHERE DriverId = '$did' AND status != 'Completed'";
    $result1 = mysqli_query($conn, $sql1);
    $numOfIncompleteRides2 = mysqli_num_rows($result1);

    $numOfCompletedRides = $numOfCompletedRides1 + $numOfCompletedRides2;

    $numOfIncompleteRides = $numOfIncompleteRides1 + $numOfIncompleteRides2;

    // Query to fetch all rides
    $sql4 = "SELECT * FROM booking_detail WHERE driver_id = '$did'";
    $result4 = mysqli_query($conn, $sql4);

    // Fetch total income
    $sql8 = "SELECT SUM(payment) AS total_income FROM booking_detail b JOIN notifications n ON DriverId = driver_id WHERE driver_id = '$did' AND status = 'Completed'";
    $result8 = mysqli_query($conn, $sql8);
    $totalIncome = 0; // Initialize total income variable

    if ($result8) {
        $row8 = mysqli_fetch_assoc($result8);
        $totalIncome = $row8['total_income'];
    } else {
        echo "Error fetching total income: " . mysqli_error($conn);
    }

    // Fetch total expenses
    $sql7 = "SELECT SUM(total) AS total_expense FROM expenses_details WHERE driver_id = '$did'";
    $result7 = mysqli_query($conn, $sql7);
    $totalExpense = 0; // Initialize total expense variable

    if ($result7) {
        $row7 = mysqli_fetch_assoc($result7);
        $totalExpense = $row7['total_expense'];
    } else {
        echo "Error fetching total expenses: " . mysqli_error($conn);
    }

    // Fetch office cash
    $sql = "SELECT office_cash FROM booking_detail WHERE driver_id = '$did'";
    $result = mysqli_query($conn, $sql);
    $officeCash = 0; // Initialize office cash variable

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if (isset($row['office_cash'])) {
            $officeCash = $row['office_cash'];
        } else {
        }
    } else {
        echo "Error fetching office cash: " . mysqli_error($conn);
    }

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif (isset($_GET['updateridstart'])) {
        echo "start";
        $rid = $_GET['id'];
        $sql4 = "UPDATE booking_detail SET ride_status='Started' WHERE id = '$rid'";
        echo $sql4;
        $result4 = mysqli_query($conn, $sql4);
        if ($result4 == true) {
            echo '<script type="text/javascript">alert("Updated Successfully");
           window.location.href = "driverdashboard.php";
           </script>';
        }
    } elseif (isset($_GET['updatecomplete'])) {
        echo "complete";
        $rid = $_GET['id'];
        $sql4 = "UPDATE booking_detail SET ride_status='Completed' WHERE id = '$rid'";
        echo $sql4;
        $result4 = mysqli_query($conn, $sql4);
        if ($result4 == true) {
            echo '<script type="text/javascript">alert("Updated Successfully");
           window.location.href = "driverdashboard.php";
           </script>';
        }
    } elseif (isset($_POST['filterRides'])) {
        $status = $_POST['ridestatus'];
        if ($status == 'All') {
            $sql4 = "SELECT * FROM booking_detail WHERE driver_id = '$did'";
            $result4 = mysqli_query($conn, $sql4);
        } else {
            $sql5 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status ='$status'";
            $result4 = mysqli_query($conn, $sql5);
        }
    }
} else {
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Driver Dashboard</title>

    <!-- Montserrat Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <style>
        h2 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin: 10px;
        }

        form select,
        form button {
            padding: 10px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        @media (max-width: 600px) {
            form {
                flex-direction: column;
            }

            .form-group {
                width: 100%;
            }
        }
    </style>

    
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
            width: 80%; /* Adjusted width for 4 cards in a row */
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
        
        .card h1 {
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
        <?php
            include 'cardStyles.php';
?>
    

</head>

<body onload="renderChart()">
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
                    <a href="driverdashboard.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="driverNotifications.php">
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
                        <a href="add_ride_bydriver.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Ride</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="add_payment_bydriver.php">
                            <i class='bx bxs-car'></i>
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
                        <a href="driverincomereport.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Trip List</span>
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
                        <a href="driverexpensereport.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Expenses Report</span> 
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
                        <a href="driverDeposits.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Deposits</span>
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
                <h2>DRIVER DASHBOARD</h2>
            </div>

            <div class="main-cards">

            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h2>Add Rides</h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>
            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h3>Completed Rides</h3>
                    <h2><?php echo $numOfCompletedRides; ?></h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>
            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128661;</div>
                    <h3>Pending Rides</h3>
                    <h2><?php echo $numOfIncompleteRides; ?></h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>

            <a href="driverexpensereport.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128176;</div>
                    <h3>Total Expenses</h3>
                    <h2><?php echo $totalExpense; ?></h2>
                </div>
                <div class="card-popup">Add Expenses</div>
            </a>

            <a href="driverincomereport.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128178;</div>
                    <h3>Total Income</h3>
                    <h2><?php echo $totalIncome; ?></h2>
                </div>
                <div class="card-popup">Trip List</div>
            </a>
            
            <a href="driverexpensereport.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128179;</div>
                    <h3>Office Cash</h3>
                    <h2><?php echo $officeCash; ?></h2>
                </div>
                <div class="card-popup">Expense Report</div>
            </a>
               
            </div>
            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                    <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>
                    <li style="list-style: none;">
                        <form id="filterForm" action="driverdashboard.php" method="POST">
                            <div class="form-group">
                                <label for="preference">Preference:</label>
                                <select id="ridestatus" name="ridestatus">
                                    <option value="All">All</option>
                                    <option value="Completed">Rides Completed</option>
                                    <option value="Assigned">Rides Pending</option>
                                    <option value="Started">Rides In Progress</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <br>
                                <label></label>
                                <button name="filterRides" type="submit">Filter</button>
                            </div>
                        </form>
                    </li>
                </ul>
                <table class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Passengers</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Mode</th>
                            <th scope="col">Booking Date</th>
                            <th scope="col">Booking Time</th>
                            <th scope="col">Ride Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result4)) {
                            echo '
            <tr>
                <td scope="row">' . $row['customer_name'] . '</td>
                <td scope="row">' . $row['NumOfPassengers'] . '</td>
                <td scope="row">' . $row['pickup_location'] . '</td>
                <td scope="row">' . $row['drop_location'] . '</td>
                <td scope="row">' . $row['mode'] . '</td>
                <td scope="row">' . $row['pickup_date'] . '</td>
                <td scope="row">' . $row['pickup_time'] . '</td>
                <td scope="row">' . $row['ride_status'] . '</td>
                <td scope="row"><a style="text-decoration:none; color:white;" href="completeRide.php?updateride&id=' . $row['id'] . '">completed</a></td>
                </tr>
            ';
                        }
                        ?>
                    </tbody>
                </table>
            </div>



        </main>
        <!-- End Main -->
    </div>
    <!-- Scripts -->
    
 <script src="myScript.js"></script> <!-- Link to your JavaScript file -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
    <script>
        function printTable() {
            var divToPrint = document.getElementById('ReportToBePrinted').getElementsByTagName('table')[0].outerHTML;
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html><head><title>Print</title><style>table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;}</style></head><body>' + divToPrint + '</body></html>');
            newWin.document.close();
            newWin.print();
            setTimeout(function () { newWin.close(); }, 10);
        }
    </script>
</body>

</html>