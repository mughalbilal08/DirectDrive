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

    $sql2 = "SELECT SUM(total) AS total FROM expenses_details;";
    $result2 = mysqli_query($conn, $sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];

    $sql3 = "SELECT * FROM booking_detail WHERE ride_status = 'Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);

    $sql4 = "SELECT * FROM booking_detail WHERE ride_status != 'Completed'";
    $result4 = mysqli_query($conn, $sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);

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

            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                    <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>

                </ul>
                <table class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Picture</th>
                            <th scope="col">Name</th>
                            <th scope="col">Number</th>
                            <th scope="col">Year</th>
                            <th scope="col">Chassis Number</th>
                            <th scope="col">Insurance Number</th>
                            <th scope="col">Insurance Company</th>
                            <th scope="col">Insurance Issue Date</th>
                            <th scope="col">Insurance Expiry Date</th>
                            <th scope="col">Registration Issue Date</th>
                            <th scope="col">Registration Expiry Date</th>
                            <th scope="col">RTA Permit</th>
                            <th scope="col">RTA Issue Date</th>
                            <th scope="col">RTA Expiry Date</th>
                            <th scope="col">Actions</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql6 = "SELECT * FROM vehicle_details";
                        $result6 = mysqli_query($conn, $sql6);
                        while ($row = mysqli_fetch_assoc($result6)) {
                            ?>
                <tr>

                    <td scope="row">
                        <a href="<?= "vehicleimages/" ?>" target="_blank">
                            <img src="vehicleimages/' . $row['Picture'] . '" alt="Image" width="50" height="50">    
                        </a>
                    </td>
                
                    <td scope="row"><?php echo $row['Name']; ?></td>
                    <td scope="row"><?php echo $row['Number']; ?></td>
                    <td scope="row"><?php echo $row['Year']; ?></td>
                    <td scope="row"><?php echo $row['Chasis']; ?></td>
                    <td scope="row"><?php echo $row['InNumber']; ?></td>
                    <td scope="row"><?php echo $row['InCompany']; ?></td>
                    <td scope="row"><?php echo $row['InIDate']; ?></td>
                    <td scope="row"><?php echo $row['InEDate']; ?></td>
                    <td scope="row"><?php echo $row['RIIDate']; ?></td>
                    <td scope="row"><?php echo $row['RIEDate']; ?></td>
                    <td scope="row"><?php echo $row['RTAPNum']; ?></td>
                    <td scope="row"><?php echo $row['RTAPIDate']; ?></td>
                    <td scope="row"><?php echo $row['RTAPEDate']; ?></td>
                    <td scope="row"><a href="update_vehicle.php?id=<?php echo $row['id']; ?>" style="color: white;">Update</a></td>
                    <td scope="row"><a href="delete_vehicle.php?id=<?php echo $row['id']; ?>" style="color: white;">Delete</a></td>
            
                </tr>';
                <?php
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
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
    <script>
    function printTable() {
        var tableContent = document.getElementById('ReportToBePrinted');
        
        // Check if there is content to print
        if (tableContent.innerHTML.trim() === '') {
            alert('No data available to print.');
            return;
        }

        // Clone the table to modify its content for printing
        var tableToPrint = tableContent.getElementsByTagName('table')[0].cloneNode(true);

        // Remove the last two columns (Actions)
        var rows = tableToPrint.getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) {
            rows[i].deleteCell(-1); // Delete last cell (second last column)
            rows[i].deleteCell(-1); // Delete second last cell (last column after deletion)
        }

        var divToPrint = tableToPrint.outerHTML;

        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<html><head><title>Print</title><style>table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;}</style></head><body>' + divToPrint + '</body></html>');
        newWin.document.close();

        // Wait for a short delay to ensure content is fully loaded
        setTimeout(function() {
            newWin.print();
            newWin.close();
        }, 100); // Adjust delay time if needed
    }
</script>



</body>

</html>

