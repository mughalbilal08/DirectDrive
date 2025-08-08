<?php
include('connect.php');
session_start();
if($_SESSION['loggedin']==true){
    
    $dId = $_SESSION['id'];
    $sql10 = "SELECT * FROM booking_detail WHERE driver_id = '$dId' AND ride_status = 'Completed'";
    $result10 = mysqli_query($conn, $sql10);
    $numOfCompletedRides = mysqli_num_rows($result10);

    $sql1 = "SELECT * FROM booking_detail WHERE driver_id = '$dId' AND ride_status != 'Completed'";
    $result1 = mysqli_query($conn, $sql1);
    $numOfIncompleteRides = mysqli_num_rows($result1);

    $sql4 = "SELECT * FROM booking_detail WHERE driver_id = '$dId'";
    $result4 = mysqli_query($conn, $sql4);

    // Fetch total income
    $sql8 = "SELECT SUM(payment) AS total_income FROM booking_detail WHERE driver_id = '$dId' AND ride_status = 'Completed'";
    $result8 = mysqli_query($conn, $sql8);
    $totalIncome = mysqli_fetch_assoc($result8)['total_income'];

    // Fetch total expenses
    $sql7 = "SELECT SUM(total) AS total_expense FROM expenses_details WHERE driver_id = '$dId'";
    $result7 = mysqli_query($conn, $sql7);
    $totalExpense = mysqli_fetch_assoc($result7)['total_expense'];


    
    $sql3 = "SELECT * FROM notifications WHERE DriverId = $dId AND status <> 'Completed' AND status <> 'Declined'";
    $result3 = mysqli_query($conn,$sql3);

    $sqlDrivers = "SELECT DISTINCT username FROM driver_details;";
    $resultDrivers = mysqli_query($conn, $sqlDrivers);

    $notificationId = 1;


    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }else if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
       
        $notificationId = $_POST['notificationId'];

        if (isset($_POST['accept'])) {
            // Validate driver selection
            
                // Update the notification with the selected driver
                $query = "UPDATE notifications 
                          SET status = 'Accepted' 
                          WHERE Id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $notificationId);
    
                if ($stmt->execute()) {
                    echo "Notification updated successfully.";
                } else {
                    echo "Failed to update notification.";
                }
    
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']); // Reload the page
                exit(); // Ensure that the script stops executing after the redirect
            
        } elseif (isset($_POST['decline'])) {
            // Handle the decline logic
            $query = "UPDATE notifications SET status = 'Declined' WHERE Id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $notificationId);
    
            if ($stmt->execute()) {
                echo "Notification declined successfully.";
            } else {
                echo "Failed to decline notification.";
            }
    
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']); // Reload the page
            exit(); // Ensure that the script stops executing after the redirect
        }
        elseif (isset($_POST['completed'])) {
            // Handle the decline logic
            $query = "UPDATE notifications SET status = 'Completed' WHERE Id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $notificationId);
    
            if ($stmt->execute()) {
                echo "Ride Completed successfully.";
            } else {
                echo "Failed to Send notification.";
            }
    
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']); // Reload the page
            exit(); // Ensure that the script stops executing after the redirect
        }
    }
    
    
    
    
}else{
    header("location:login.php");
}
?> 
 
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
            include 'driverSideBar.php';
?>

            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>
                </ul>
                <table title="DriverDetails" class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            
                            <th scope="col">Notification</th>
                            <th scope="col">From</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                            <th scope="col">Action</th>
                            <th scope="col">Action</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          
                       while($row =mysqli_fetch_assoc($result3)){
                        // Fetch profile picture path from database
                         
                            
                        ?>
                        <tr>

                           
                            <td scope="row"><?php echo $row['message']; ?></td>
                            <td scope="row"><?php echo $row['role']; ?></td>
                            <td scope="row"><?php echo $row['status']; ?></td>

                            <form id="filterForm" method="POST">
                            <input type="hidden" name="notificationId" value="<?php echo $row['id']; ?>">
                            <td scope="row"> 
                                <button type="submit" name="accept" class="button">Accept</button>
                            </td>
                            <td scope="row"> 
                                <button type="submit" name="decline" class="button">Decline</button>
                            </td>
                            <td scope="row"> 
                                <button type="submit" name="completed" class="button">Completed</button>
                            </td>
                            </form>
                            
                            
                            </tr>
                        
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