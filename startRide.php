<?php
include('connect.php');
session_start();
if($_SESSION['loggedin']==true){
    $did = $_SESSION['id'];
    $sql3 ="select * from booking_detail where driver_id  = '$did' and ride_status ='Completed'";
    $result3 = mysqli_query($conn,$sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);
    $sql1 ="select * from booking_detail where driver_id  = '$did' and ride_status !='Completed'";
    $result1 = mysqli_query($conn,$sql1);
    $numOfIncompleteRides = mysqli_num_rows($result1);
    $sql4 ="select * from booking_detail where driver_id  = '$did'";
    $result4 = mysqli_query($conn,$sql4);
    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif(isset($_POST['update-ride'])){
        $rid =$_POST['rideId'];
        $Vehicle = $_POST['vehicle'];
          $sql4 = "Update booking_detail set vehicle = '$Vehicle', ride_status='Started' where id = '$rid' ";
         echo $sql4;
        $result4 = mysqli_query($conn,$sql4);
        if($result4==true){
            echo '<script type="text/javascript">alert("Submitted Successfully");
            window.location.href = "driverdashboard.php";
            </script>';
        }
      } elseif (isset($_GET['updateride'])) {
        $rideId=$_GET['id'];
        $sql3 ="select * from booking_detail where id ='$rideId'";
        $result3 = mysqli_query($conn,$sql3);
        $row3 = mysqli_fetch_assoc($result3);
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
                    <label><?php echo $_SESSION['name'];?></label> 
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
                    <a href="driverdashboard.php" >
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="add_vehicleby_driver.php" >
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Add Vehicle</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="driverdashboard.php?logout" >
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

                <div class="card">
                    <div class="card-inner">
                        <h3>Completed Rides</h3>
                     </div>
                    <h1><?php echo $numOfCompletedRides;?></h1>
                </div>
                <div class="card">
                    <div class="card-inner">
                        <h3>Pending Rides</h3>
                    </div>
                    <h1><?php echo$numOfIncompleteRides; ?></h1>
                </div>
            </div>

            <div id="container">
                <div class="container">
                    <h2>Update Ride</h2>
                    <form  autocorrect ="off" autocomplete="off" action="startRide.php" method="POST">
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
                            <label for="profileImage">Select Vehicle</label>
                            <?php 
                            $sql4= "select id,Name from vehicle_details";
                            $result4 = mysqli_query($conn,$sql4);
                            ?>
                            <select class="selector" name="vehicle" id = "vehicle">
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
                        <button type="submit" name="update-ride"  class="button">Start Ride</button>
                    </form>
                </div>
            </div>

        </main>
        <!-- End Main -->
    </div>
    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
</body>

</html>