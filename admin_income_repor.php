<?php
include('connect.php');
session_start();

// Check if the user is logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
    // Check if the user's session variables are set
    if(isset($_SESSION['email'], $_SESSION['name'])){
        $username = $_SESSION['name'];
    } else {
        $username = "Unknown";
    }

    // Get the number of drivers
    $sql0 = "SELECT * FROM driver_details WHERE role ='Driver'";
    $result0 = mysqli_query($conn, $sql0);
    $numberofDrivers = mysqli_num_rows($result0);

    // Get the number of customers
    $sql1 = "SELECT * FROM user_accounts WHERE role ='customer'";
    $result1 = mysqli_query($conn, $sql1);
    $numberofCustomers = mysqli_num_rows($result1);

    // Get the total expenses
    $sql2 = "SELECT SUM(total) AS total FROM expenses_details";
    $result2 = mysqli_query($conn, $sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];

    // Get the number of completed rides
    $sql3 = "SELECT * FROM booking_detail WHERE ride_status ='Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);

    // Get the number of incomplete rides
    $sql4 = "SELECT * FROM booking_detail WHERE ride_status != 'Completed'";
    $result4 = mysqli_query($conn, $sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);

    // Initialize the complete array
    $completeArray = array();

    

    // Check if date filter is applied
    if(isset($_GET['filterRides']) && isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] != '' && $_GET['end_date'] != '') {
        $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
        $sql6 = "SELECT * FROM booking_detail WHERE (pickup_date) BETWEEN '$start_date' AND '$end_date'";
        
    
    } else {
        $sql6 = "SELECT * FROM booking_detail";
    }

    // Execute SQL query
   
    $result6 = mysqli_query($conn, $sql6);

    // Check for errors
    if(!$result6) {
        echo "Error executing SQL query: " . mysqli_error($conn);
    }
} else {
    // Redirect to login page if not logged in
    header("location:login.php");
    exit; // Stop executing the rest of the code
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    

<?php
include 'cardStyles.php';
?>



</head>

<body>
<div class="grid-container">
        
<?php
            include 'adminSideBar.php';
?>



            <!-- Date filter and Ride Reports -->
            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>

                    <li style="list-style: none;">
                        <form method="GET">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <ul>
                        <div>
                            <br>
                            <br>
                            <button name="filterRides" type="submit">Filter</button>
                        </div>

                        </ul>
                       
                            
                        </form>
                    </li>
                </ul>
                <table class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Ride ID</th>
                            <th scope="col">Passengers</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Booking Date</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display ride reports based on filtered or unfiltered query
                        while($row = mysqli_fetch_assoc($result6)) {
                            echo '
                            <tr>
                                <td>'.$row['id'].'</td>
                                <td>'.$row['customer_name'].'</td>
                                <td>'.$row['pickup_location'].'</td>
                                <td>'.$row['drop_location'].'</td>
                                <td>'.$row['pickup_date'].'</td>
                                <td>'.$row['payment'].'</td>
                            </tr>';
                        }

                        // Display total payment summary row
                        $sql10 = "SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail WHERE 1 = 1";

                        if (isset($_GET['filterRides']) && isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] != '' && $_GET['end_date'] != '') {
                            $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
                            $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
                            $sql10 .= " AND pickup_date BETWEEN '$start_date' AND '$end_date'";
                        }

                        $result10 = mysqli_query($conn, $sql10);
                        $total = mysqli_fetch_assoc($result10);
                        echo '
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>'.$total['total'].'</strong></td>
                        </tr>';
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
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
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

