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

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("Location: index.html");
    } elseif (isset($_GET['updateride'])) {
        $rideId = $_GET['id'];
        $sql3 = "SELECT * FROM booking_detail WHERE id = '$rideId'";
        $result3 = mysqli_query($conn, $sql3);
        $row3 = mysqli_fetch_assoc($result3);
    } elseif (isset($_POST['update-ride'])) {
        $rid = $_POST['rideId'];
        $driverId = $_POST['driver'];

        // Update the ride details
        $sql4 = "UPDATE booking_detail SET driver_id = '$driverId', ride_status='Assigned' WHERE id = '$rid'";
        $result4 = mysqli_query($conn, $sql4);

        if ($result4 == true) {
            // Retrieve the updated ride details
            $sql3 = "SELECT * FROM booking_detail WHERE id = '$rid'";
            $result3 = mysqli_query($conn, $sql3);
            $row3 = mysqli_fetch_assoc($result3);

            // Retrieve driver's email from driver_details table
            $sql5 = "SELECT email, Name FROM driver_details WHERE id = '$driverId'";
            $result5 = mysqli_query($conn, $sql5);
            $row5 = mysqli_fetch_assoc($result5);
            $driverEmail = $row5['email'];
            $driverName = $row5['Name'];

            // Retrieve customer's email from user_accounts table
            $customerId = $row3['customer_id'];
            $sql6 = "SELECT email FROM user_accounts WHERE id = '$customerId'";
            $result6 = mysqli_query($conn, $sql6);
            $row6 = mysqli_fetch_assoc($result6);
            $customerEmail = $row6['email'];

            // Email message to driver
            $toDriver = $driverEmail;
            $subjectDriver = 'New Ride Assigned';
            $messageDriver = "Dear Driver,\n\nA new ride has been assigned to you. Here are the details:\n\n" .
                       "Customer Name: " . $row3['customer_name'] . "\n" .
                       "Pickup Location: " . $row3['pickup_location'] . "\n" .
                       "Drop Off Location: " . $row3['drop_location'] . "\n" .
                       "Pickup Date: " . $row3['pickup_date'] . "\n" .
                       "Pickup Time: " . $row3['pickup_time'] . "\n" .
                       "Number of Passengers: " . $row3['NumOfPassengers'] . "\n\n" .
                       "Please check your dashboard for more details.\n\nThank you.";
            $headersDriver = 'From: your_email@example.com' . "\r\n" .
                       'Reply-To: your_email@example.com' . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();

            // Email message to customer
            $toCustomer = $customerEmail;
            $subjectCustomer = 'Ride Confirmation';
            $messageCustomer = "Dear Customer,\n\nYour ride has been confirmed. Here are the details:\n\n" .
                       "Driver Name: " . $row5['Name'] . "\n" .
                       "Pickup Location: " . $row3['pickup_location'] . "\n" .
                       "Drop Off Location: " . $row3['drop_location'] . "\n" .
                       "Pickup Date: " . $row3['pickup_date'] . "\n" .
                       "Pickup Time: " . $row3['pickup_time'] . "\n" .
                       "Number of Passengers: " . $row3['NumOfPassengers'] . "\n\n" .
                       "Please contact us if you have any questions.\n\nThank you.";
            $headersCustomer = 'From: your_email@example.com' . "\r\n" .
                       'Reply-To: your_email@example.com' . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();

            // Send email to driver
            $emailSentToDriver = mail($toDriver, $subjectDriver, $messageDriver, $headersDriver);

            // Send email to customer
            $emailSentToCustomer = mail($toCustomer, $subjectCustomer, $messageCustomer, $headersCustomer);

            if ($emailSentToDriver && $emailSentToCustomer) {
                echo '<script type="text/javascript">
                    alert("Ride assigned successfully. Emails sent to driver and customer.");
                    window.location.href = "reports.php";

                    </script>';

            } else {
                echo '<script type="text/javascript">
                    alert("Ride assigned successfully, but failed to send emails.");
                    window.location.href = "dashboard.php";
                    </script>';
            }
        }
    }
} else {
    header("Location: login.php");
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
            <div id="container">
                <div class="container">
                    <h2>Update Ride</h2>
                    <form  autocorrect ="off" autocomplete="off" action="update_ride.php" method="POST">
                    <input style="color: black; visibility:hidden;"  readonly autocorrect ="off" value="<?php echo $row3['id'];?>" aria-autocomplete="off" autocomplete="off" type="text" id="rideId" name="rideId" required> 
                     <div class="form-group">
                            <label for="name">Customer Name<span style="color: #f90d0d;">*</span></label>
                            <input style="color: black;" readonly autocorrect ="off" value="<?php echo $row3['customer_name'];?>" aria-autocomplete="off" autocomplete="off" type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="pickup-location">Pickup Location</label>
                            <input  autocomplete="off" readonly value="<?php echo $row3['pickup_location'];?>" type="text" id="pickup-location" name="pickup-location">
                        </div>
                        <div class="form-group">
                            <label for="drop-location">Drop Off Location</label>
                            <input autocomplete="off" readonly value="<?php echo $row3['drop_location'];?>"  type="text" id="drop-location" name="drop-location" rows="3"></input>
                        </div>
                        <div class="form-group">
                            <label for="pickup-date">Pickup Date<span
                                    style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" readonly value="<?php echo $row3['pickup_date'];?>"  type="text" id="pickup-date" name="pickup-date" required onclick="generateUsername()">
                        </div>
                        <div class="form-group">
                            <label for="pickup-time">Pickup Time<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" readonly value="<?php echo $row3['pickup_time'];?>"  type="text" id="pickup-time" name="pickup-time" required>
                        </div>
                        <div class="form-group">
                            <label for="passengers">Passengers<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" readonly  value="<?php echo $row3['NumOfPassengers'];?>"  type="tel" id="passengers" name="passengers" required>
                        </div>
                        <div class="form-group">
                            <label for="profileImage">DriverName</label>
                            <?php 
                            $sql4= "select id,Name from driver_details";
                            $result4 = mysqli_query($conn,$sql4);
                            ?>
                            <select class="selector" name="driver" id = "driver">
                                <option value="" >-------------</option>
                            <?php while ($row4 = mysqli_fetch_assoc($result4)){?>
                            <option value="<?= htmlspecialchars($row4['id']) ?>">
                              <?= htmlspecialchars($row4['Name']) ?>
                            </option>
                            <?php 
                        }?>
                             </select>
                        </div>
                        <label>* indicate required fields</label>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="update-ride"  class="button">Assign</button>
                    </form>
                </div>
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
</body>

</html>