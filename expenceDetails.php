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
    $sql3 ="select username, sum(payment) Earned, sum(b.deposits) Deposit, sum(total) Expenses,(sum(payment) - (sum(b.deposits) + sum(total))) Remaining  from driver_details d join booking_detail b on d.id = b.driver_id join expenses_details e ON e.driver_id = d.id group by username";
    $result3 = mysqli_query($conn,$sql3);
    $sqlDrivers = "SELECT DISTINCT username FROM driver_details;";
    $resultDrivers = mysqli_query($conn, $sqlDrivers);
    $sqlvehicles = "SELECT DISTINCT Number FROM vehicle_details";
    $resultvehicles = mysqli_query($conn, $sqlvehicles);

    $tSelector = 0;


    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }elseif(isset($_POST['filterModes']) && isset($_POST['start_date']) && isset($_POST['end_date']) && $_POST['start_date'] != '' && $_POST['end_date'] != '') {
        $tSelector = 1;
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $sql10 = "SELECT IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) AS Earned, IFNULL(SUM(deposits), 0) AS Deposit, IFNULL((SELECT SUM(total) FROM expenses_details WHERE date BETWEEN '$start_date' AND '$end_date'), 0) AS Expenses, IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) - IFNULL((SELECT SUM(total) FROM expenses_details WHERE date BETWEEN '$start_date' AND '$end_date'), 0) AS Balance FROM booking_detail WHERE pickup_date BETWEEN '$start_date' AND '$end_date';";
        $result3 = mysqli_query($conn,$sql10);
    }elseif(isset($_POST['filterRides']) && isset($_POST['start_date']) && isset($_POST['end_date']) && $_POST['start_date'] != '' && $_POST['end_date'] != '') {
        $tSelector = 0;
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $sql9 = "SELECT username, IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) AS Earned, IFNULL(SUM(deposits), 0) Deposit, sum(total) Expenses,(IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) - (IFNULL(SUM(deposits), 0) + IFNULL(SUM(total), 0))) Remaining  from driver_details d join booking_detail b on d.id = b.driver_id join expenses_details e ON e.driver_id = d.id where  pickup_date BETWEEN '$start_date' AND '$end_date' group by username";
        $result3 = mysqli_query($conn,$sql9);
    }
    elseif(isset($_POST['filterModes']))
    {
        
        $tSelector = 1;
        $sql8 ="SELECT IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) Earned, IFNULL(SUM(deposits), 0) Deposit, (select IFNULL(SUM(total), 0) from expenses_details) Expenses,(IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) - (select IFNULL(SUM(total), 0) from expenses_details)) Balance  from booking_detail where 1 = 1";
        $result3 = mysqli_query($conn,$sql8);
       
       
    }    
     elseif(isset($_POST['filterRides'])){
        $tSelector = 0;
        $sql9 ="SELECT username, IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) Earned, IFNULL(SUM(deposits), 0) Deposit, IFNULL(SUM(total), 0) Expenses,(IFNULL(SUM(CAST(payment AS UNSIGNED)), 0) - (IFNULL(SUM(deposits), 0) + IFNULL(SUM(total), 0))) Remaining  from driver_details d join booking_detail b on d.id = b.driver_id join expenses_details e ON e.driver_id = d.id group by username";
        $result3 = mysqli_query($conn,$sql9);

    
     }          
}
else{
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
        <?php
include 'cardStyles.php';
?>

</head>

<body>
<div class="grid-container">
<?php
            include 'adminSideBar.php';
?>
            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>
                    <li style="list-style: none;">
                    <form id="filterForm" method="POST" action="expenceDetails.php">
           
                    

            
            <div class="form-group">
                <button name="filterRides" type="submit">Driver Expenses</button>
            </div>

            
            <div class="form-group">
                <button name="filterModes" type="submit">Show Profit</button>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            
           
        </form>
                    </li>
                </ul>
                <?php if ($tSelector == 0): ?>
    <table class="table" style="text-align: center;">
        <thead class="thead-dark">
            <tr>
                <th scope="col">username</th>
                <th scope="col">Earned</th>
                <th scope="col">Deposits</th>
                <th scope="col">Expenses</th>
                <th scope="col">Remaining</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result3)): ?>
                <tr>
                    <td scope="row"><?= $row['username'] ?></td>
                    <td scope="row"><?= $row['Earned'] ?></td>
                    <td scope="row"><?= $row['Deposit'] ?></td>
                    <td scope="row"><?= $row['Expenses'] ?></td>
                    <td scope="row"><?= $row['Remaining'] ?></td>
                    
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <table class="table" style="text-align: center;">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Earned</th>
                <th scope="col">Deposits</th>
                <th scope="col">Expenses</th>
                <th scope="col">Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result3)): ?>
                <tr>
                    <td scope="row"><?= $row['Earned'] ?></td>
                    <td scope="row"><?= $row['Deposit'] ?></td>
                    <td scope="row"><?= $row['Expenses'] ?></td>
                    <td scope="row" style="color: <?= $row['Balance'] < 0 ? 'red' : 'white' ?>;"><?= $row['Balance'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

                
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

        document.getElementById('addOptionButton').addEventListener('click', function() {
        // Create a new option element
        var newOption = document.createElement('option');
        newOption.value = 'NewOptionValue'; // Set the value attribute
        newOption.text = 'New Option'; // Set the text content

        // Add the new option to the select element
        var selectElement = document.getElementById('ridestatus');
        selectElement.appendChild(newOption);
        });
    </script>
   
    
</body>

</html>