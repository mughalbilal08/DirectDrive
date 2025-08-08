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
    
    $sql3 = "SELECT username, vehicle, customer_name, newType, mode, IFNULL((payment), 0) AS total, pickup_date, rideDistance, driverTime, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND pickup_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND CURDATE()";
    $result3 = mysqli_query($conn,$sql3);
    $sql11 ="SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND pickup_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND CURDATE();";
    $result10 = mysqli_query($conn, $sql11);
    $total = mysqli_fetch_assoc($result10);
    $sqlDrivers = "SELECT DISTINCT username FROM driver_details;";
    $resultDrivers = mysqli_query($conn, $sqlDrivers);
    $sqlvehicles = "SELECT DISTINCT Number FROM vehicle_details";
    $resultvehicles = mysqli_query($conn, $sqlvehicles);

    
    $pMode = isset($_POST['pMode']) ? $_POST['pMode'] : 'All';

    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }    
     elseif(isset($_POST['filterRides'])){
        $username = $_POST['username'];
        $vehicle = $_POST['vehicle'];
        $sql10 = "SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail WHERE 1 = 1";

          
        
    
             
       
        $sql10 ="SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND pickup_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND CURDATE()";
        $sql4 = "SELECT username, vehicle, customer_name, newType, mode, IFNULL((payment), 0) AS total, pickup_date, rideDistance, driverTime, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id  WHERE 1=1 AND pickup_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND CURDATE()";
        
        if( isset($_POST['start_date']) && isset($_POST['end_date']) && $_POST['start_date'] != '' && $_POST['end_date'] != ''){

            $sql4 ="SELECT username, vehicle, customer_name,newType, mode, IFNULL((payment), 0) AS total, pickup_date, rideDistance, driverTime, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1";
            $sql10 ="SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1";
            
            $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
            $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
            $sql4 .= " AND pickup_date BETWEEN '$start_date' AND '$end_date'";
            $sql10 .= " AND pickup_date BETWEEN '$start_date' AND '$end_date'";
        }
        
        if($username !='All'){
            $sql4 .= " AND username = '$username'";
            $sql10 .= " AND username = '$username'";
           
        }
        
        if($vehicle != 'All'){
            $sql4 .= " AND vehicle = '$vehicle'";
            $sql10 .= " AND vehicle = '$vehicle'";
        }
        
        if($pMode != 'All'){
            $sql4 .= " AND mode = '$pMode'";
            $sql10 .= " AND mode = '$pMode'";
        }
       

        $result3 = mysqli_query($conn,$sql4);
        $result10 = mysqli_query($conn, $sql10);
        $total = mysqli_fetch_assoc($result10);
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
        <!-- Header -->
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
                    <form id="filterForm" method="POST" action="driverDetails.php">
           
                    <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                    <div class="form-group">
                <label for="username">Drivers</label>
                    <select id="username" name="username">
                        <option value="All">All</option>
                        <?php
                            while ($row = mysqli_fetch_assoc($resultDrivers)) {
                                echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
                            }
                        ?>
                    </select>
            </div>

            <div class="form-group">
                <label for="vehicle">Vehicles</label>
                    <select id="vehicle" name="vehicle">
                        <option value="All">All</option>
                        <?php
                            while ($row = mysqli_fetch_assoc($resultvehicles)) {
                                echo '<option value="' . $row['Number'] . '">' . $row['Number'] . '</option>';
                            }
                        ?>
                    </select>
            </div>
            
            <div class="form-group">
                <label for="pMode">Payment Mode</label>
                    <select id="pMode" name="pMode">
                        <option value="All">All</option>
                        <option value="Cash">Cash</option>
                        <option value="Uber">Uber</option>
                        <option value="Careem">Careem</option>
                        <option value="MBR">MBR</option>
                        <option value="GM/Office">GM/Office</option>
                        <option value="Voucher">Voucher</option>
                        <option value="Personal Cash">Personal Cash</option>
                        <option value="Network">Network</option>
                        <option value="Other">Other</option>
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
                <th scope="col">username</th>
                <th scope="col">vehicle</th>
                <th scope="col">customer_name</th>
                <th scope="col">Mode</th>
                <th scope="col">Type</th>
                <th scope="col">total</th>
                <th scope="col">Pickup Date</th>
                <th scope="col">Kilo Meters</th>
                <th scope="col">Ride Time</th>
                <th scope="col">Ride Picture</th>
                
                    
               
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result3)): ?>
                <?php $pic =  $row['ride_picture'] ?>
                <tr>
                    <td scope="row"><?= $row['username'] ?></td>
                    <td scope="row"><?= $row['vehicle'] ?></td>
                    <td scope="row"><?= $row['customer_name'] ?></td>
                    <td scope="row"><?= $row['mode'] ?></td>
                    <td scope="row"><?= $row['newType'] ?></td>
                    <td scope="row"><?= $row['total'] ?></td>
                    <td scope="row"><?= $row['pickup_date'] ?></td>                    
                    <td scope="row"><?= 0 +  $row['rideDistance'] ?></td>
                    <td scope="row"><?= $row['driverTime'] ?></td>
                    
                    <?php if ($row['mode'] == "Network" || $row['mode'] == "Voucher") { ?>
                        <td scope="row">
                            <a href="<?= $pic ?>" target="_blank">
                                <img src="<?= $pic ?>" alt="N/A" width="50" height="50">
                            </a>
                        </td>
                    <?php } else { ?>
                        <td scope="row">N/A</td>
                    <?php } ?>
                </tr>
            <?php endwhile; ?>
            <?php
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
    <script>
        function printTable() {
            var divToPrint = document.getElementById('ReportToBePrinted').getElementsByTagName('table')[0].outerHTML;
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html><head><title>Print</title></head><body><style>@media print { table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;} td[style*="background-color:red"] {background-color: red !important; color: white;} td[style*="background-color:yellow"] {background-color: yellow !important; color: black;} td[style*="background-color:green"] {background-color: green !important; color: white;}}</style>' + divToPrint + '</body></html>');
            newWin.document.close();
            newWin.print();
            setTimeout(function () {
                newWin.print();
                newWin.close();
            }, 1000);
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