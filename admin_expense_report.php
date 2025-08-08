<?php
include('connect.php');
session_start();
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
    // Check if the user's session variables are set
    if(isset($_SESSION['email'], $_SESSION['name'])){
        $username = $_SESSION['name'];
    } else {
        $username = "Unknown";
    }


    $aid = $_SESSION['id'];
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
    $sql3 ="select * from booking_detail where  ride_status ='Completed'";
    $result3 = mysqli_query($conn,$sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);
    $sql4 ="select * from booking_detail where ride_status !='Completed'";
    $result4 = mysqli_query($conn,$sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);
    $completeArray = array();

    $sql6 = "SELECT * FROM expenses_details e left JOIN driver_details d ON e.driver_id = d.id WHERE 1=1";

// Add expense type filter if selected
if(isset($_GET['expense_type']) && $_GET['expense_type'] !== 'All') {
    $expense_type = mysqli_real_escape_string($conn, $_GET['expense_type']);
    $sql6 .= " AND type = '$expense_type'";
}

if(isset($_GET['addby']) && $_GET['addby'] !== 'All') {
    $addby = mysqli_real_escape_string($conn, $_GET['addby']);
    $sql6 .= " AND add_by = '$addby'";
}

// Add date range filter if start_date and end_date are provided
if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] != '' && $_GET['end_date'] != '') {
    $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
    $sql6 .= " AND date BETWEEN '$start_date' AND '$end_date'";
}

$result6 = mysqli_query($conn, $sql6);
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



<?php
include 'cardStyles.php';
?>

</head>

<body>
<div class="grid-container">
        
<?php
            include 'adminSideBar.php';
?>


             <!-- Filter form for expense report -->
        <form method="GET">
            <div class="form-group">
                <label for="expense_type">Filter by Expense Type:</label>
                <select name="expense_type" id="expense_type">
                    <option value="All">All</option>
                    <option value="CarWash">Car Wash</option>
                    <option value="BankDeposit">Bank Deposit</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Fuel">Fuel</option>
                    <option value="Misc">Misc</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="addby">Add By:</label>
                <select name="addby" id="addby">
                    <option value="All">All</option>
                    <option value="Admin">Admin</option>
                    <option value="Driver">Driver</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
            <div class="form-group">
                <br>
                <label></label>
                <button name="filterRides" type="submit">Filter</button>
            </div>
        </form>
             <!-- Expense report table -->
             <div id="ReportToBePrinted" class="reportTable">
                <ul>
                    <!-- Print button here -->
                    <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>
                </ul>
                <table class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Added By</th>
                            <th scope="col">username</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
// Modify SQL query to include expense type and date range filter


// Process and display table rows as before
while($row = mysqli_fetch_assoc($result6)) {
    echo '
    <tr>
        <td scope="row">' . $row['id'] . '</td>
        <td scope="row">' . $row['type'] . '</td>
        <td scope="row">' . $row['total'] . '</td>
        <td scope="row">' . $row['add_by'] . '</td>
        <td scope="row">' . $row['username'] . '</td>
        <td scope="row">' . $row['date'] . '</td>
    </tr>
    ';
}



// Calculate total expenses for the filtered result set
$sql7 = "SELECT ifnull(SUM(total),0) AS total FROM expenses_details WHERE 1=1";

if(isset($_GET['expense_type']) && $_GET['expense_type'] !== 'All') {
    $expense_type = mysqli_real_escape_string($conn, $_GET['expense_type']);
    $sql7 .= " AND type = '$expense_type'";
}

if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] != '' && $_GET['end_date'] != '') {
    $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
    $sql7 .= " AND date BETWEEN '$start_date' AND '$end_date'";
}

if(isset($_GET['addby']) && $_GET['addby'] !== 'All') {
    $addby = mysqli_real_escape_string($conn, $_GET['addby']);
    $sql7 .= " AND add_by = '$addby'";
}

$result7 = mysqli_query($conn, $sql7);
$total = mysqli_fetch_assoc($result7);

echo '
<tr>
    <td scope="row">Total</td>
    <td scope="row">' . $total['total'] . '</td>
</tr>
';
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
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>    <!-- Custom JS -->
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