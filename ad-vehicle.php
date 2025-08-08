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

    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }elseif(isset($_POST['add-vehicle'])){
	      $Name=$_POST['name'];
        	$Number=$_POST['number'];
        	$Year=$_POST['year'];	
            $Chasis =$_POST['chasis'];
            $InNumber =$_POST['InsNum'];
            $InCompany=$_POST['InCname'];
            $InIDate=$_POST['InIDate'];
            $InEDate=$_POST['InEDate'];
            $RIIDate=$_POST['RegIDate'];
            $RIEDate=$_POST['RegEDate'];
            $RTAPNum=$_POST['RtaNum'];
            $RTAPIDate=$_POST['RtaIDate'];
            $RTAPEDate=$_POST['RtaEDate'];
            $file_name = $_FILES['picture']['name'];
            $file_tmp = $_FILES['picture']['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if(in_array($file_ext, array('jpg', 'jpeg', 'png')) && !empty($file_name)) {
                $image_data = base64_encode(file_get_contents($file_tmp));
            }elseif(empty($file_name)){
                
            } else {
                echo '<script type="text/javascript">alert("Only Image files are allowed(.jpg,.png,.jpeg)");
               </script>';}
               $sql="INSERT INTO `vehicle_details` (`Name`, `Number`, `Year`, `Chasis`, `Picture`, `InNumber`, `InCompany`, 
               `InIDate`, `InEDate`, `RIIDate`, `RIEDate`, `RTAPNum`, `RTAPIDate`, `RTAPEDate`) 
               VALUES ('$Name', '$Number', '$Year', '$Chasis', '$file_name', '$InNumber', '$InCompany', '$InIDate', '$InEDate',
                '$RIIDate', '$RIEDate', '$RTAPNum','$RTAPIDate','$RTAPEDate' )";
              $res = mysqli_query($conn,$sql);
              if($res= true){
              file_put_contents("vehicleimages/".$file_name, base64_decode($image_data));
              echo '<script type="text/javascript">alert("Submitted Successfully");
              window.location.href = "view_vehicles.php";

              </script>';

              }else{
                  echo '<script type="text/javascript">alert("Not Submitted Please try again");
                         </script>';
              }
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
            include 'adminAddStyles.php';
?>




</head>

<body>
<div class="grid-container">
        
<?php
            include 'adminSideBar.php';
?>


            <div id="container">
                <div class="container">
                    <h2>Add Vehicle</h2>
                    <form id="driverForm" method="POST" enctype="multipart/form-data" action="ad-vehicle.php">
                        <div class="form-group">
                          <label for="name">Name<span style="color: #f90d0d;">*</span> </label>
                          <input type="text"  id="name" name="name" required>
                        </div>
                        <div class="form-group">
                          <label for="number">Number<span style="color: #f90d0d;">*</span> </label>
                          <input type="text" id="number" name="number" required>
                        </div>
                        <div class="form-group">
                          <label for="year">Year<span style="color: #f90d0d;">*</span> </label>
                          <input type="number" id="year" name="year" required>
                        </div>
                        <div class="form-group">
                          <label for="chasis">Chassis Number (Optional)</label>
                          <input type="text" id="chasis" name="chasis"  >
                        </div>
                        <div class="form-group">
                          <label for="picture">Picture (Optional)</label>
                          <input type="file" id="picture" name="picture"  >
                        </div>
                        <div class="form-group">
                          <label for="InsNum">Insurance Number (Optional)</label>
                          <input type="text" id="InsNum" name="InsNum">
                        </div>
                        <div class="form-group">
                          <label for="InCname">Insurance Company Name (Optional)</label>
                          <input type="text" id="InCname" name="InCname">
                        </div>
                        <div class="form-group">
                          <label for="InIDate">Insurance Issue date<span style="color: #f90d0d;">*</span> </label>
                          <input type="date" id="InIDate" name="InIDate" required>
                        </div>
                        <div class="form-group">
                          <label for="InEDate">Insurance Expiry Date<span style="color: #f90d0d;">*</span> </label>
                          <input type="date" id="InEDate" name="InEDate" required>
                        </div>
                        <div class="form-group">
                          <label for="RegIDate">Registration Issue Date<span style="color: #f90d0d;">*</span> </label>
                          <input type="date" id="RegIDate" name="RegIDate" required>
                        </div>
                        <div class="form-group">
                          <label for="RegEDate">Registration Expiry date<span style="color: #f90d0d;">*</span> </label>
                          <input type="date" id="RegEDate" name="RegEDate" required>
                        </div>
                        <div class="form-group">
                          <label for="RtaNum">RTA Permit No. (Optional)</label>
                          <input type="number" id="RtaNum" name="RtaNum">
                        </div>
                        <div class="form-group">
                          <label for="RtaIDate">RTA Issue Date (Optional)</label>
                          <input type="date" id="RtaIDate" name="RtaIDate">
                        </div>
                        <div class="form-group">
                          <label for="RtaEDate">RTA Expiry Date(Optional)</label>
                          <input type="date" id="RtaEDate" name="RtaEDate">
                        </div>
                        <label >* indicate required fileds</label>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="add-vehicle" class="button">Submit</button>
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