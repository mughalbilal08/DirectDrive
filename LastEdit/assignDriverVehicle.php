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

    $sqlCars = "SELECT DISTINCT `Number` FROM vehicle_details;";
    $resultCars = mysqli_query($conn, $sqlCars);
     

    $sql3 ="select * from booking_detail where  ride_status ='Completed'";
    $result3 = mysqli_query($conn,$sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);
    $sql4 ="select * from booking_detail where ride_status !='Completed'";
    $result4 = mysqli_query($conn,$sql4);
    $numOfIncompleteRides = mysqli_num_rows($result4);
    $completeArray = array();
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
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>


    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title:{
		text: ""
	},
	axisY:{
		includeZero: true
	},
	data: [{
		type: "column", //change type to bar, line, area, pie, etc
		//indexLabel: "{y}", //Shows y value on all Data Points
		indexLabelFontColor: "#5A5757",
		indexLabelPlacement: "outside",   
		dataPoints: <?php echo json_encode($completeArray, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}

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
            <div class="container mt-5">
        <h2>Select a Driver</h2>
        <?php
        // Include the database connection file
        include 'connect.php';

        // Check if form is submitted with driver selection
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['driver_id']) && !empty($_POST['driver_id'])) {
            $driver_id = mysqli_real_escape_string($conn, $_POST['driver_id']);


            // Query to fetch driver's office_cash
            $vehicleQuery = "SELECT (Select Number from vehicle_details Where id = vehicleId) vehicleNumber , username FROM driver_details WHERE id = '$driver_id'";
            $vehcleResult = mysqli_query($conn, $vehicleQuery);

            if ($vehcleResult && mysqli_num_rows($vehcleResult) > 0) {
                $vehicleRow_row = mysqli_fetch_assoc($vehcleResult);
                $vehicleNum = $vehicleRow_row['vehicleNumber'];
                $driver_name = $vehicleRow_row['username'];
                echo "<div class='alert alert-info mt-3'>Current Vehicle for Selected Driver $driver_name: $vehicleNum</div>";

                // Display form to update office_cash
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='driver_id' value='$driver_id'>";
                echo "<input type='hidden' name='driver_name' value='$driver_name'>";
                echo "<div class='form-group mt-3'>";
                echo "<label for='vehicleID'>Select a vehicle to assign:</label>";
                echo '<select class="selector form-control" name="vehicleID" id="vehicleID" onchange="toggleFileInput()" required>';
                echo '<option value="">-------------</option>';
                while ($row = mysqli_fetch_assoc($resultCars)) {
                    echo '<option value="' . $row['Number'] . '">' . $row['Number'] . '</option>';
                }
                echo '</select>';
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary' name='update_office_cash'>Assign Vehicle</button>";
                echo "</form>";
                
            } else {
                echo "<div class='alert alert-warning mt-3'>No office cash details found for the selected driver.</div>";
            }

           
        }

        // Handle office cash update
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_office_cash'])) {
            $driver_id = mysqli_real_escape_string($conn, $_POST['driver_id']);
            $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
            $vehicleNum = mysqli_real_escape_string($conn, $_POST['vehicleID']);

          

            
            

            // Update office_cash in booking_detail table
            $update_query = "UPDATE driver_details SET vehicleId = (Select id from vehicle_details Where Number = '$vehicleNum') WHERE id = '$driver_id'";
            $update_result = mysqli_query($conn, $update_query);

            if ($update_result) {
                echo "<div class='alert alert-success mt-3'>Vehicle Assigned successfully!</div>";

               
            } else {
                echo "<div class='alert alert-danger mt-3'>Error Assigning vehicle: " . mysqli_error($conn) . "</div>";
            }
           

           
        }

        // Fetch and display all drivers
        $query = "SELECT id, Name FROM driver_details";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<form method='POST' action=''>";
            echo "<div class='form-group'>";
            echo "<label for='driver'>Driver:</label>";
            echo "<select class='form-control' id='driver' name='driver_id' onchange='this.form.submit()'>";
            echo "<option value=''>Select Driver</option>";

            while ($row = mysqli_fetch_assoc($result)) {
                $selected = (isset($_POST['driver_id']) && $_POST['driver_id'] == $row['id']) ? 'selected' : '';
                echo "<option value='" . $row['id'] . "' $selected>" . $row['Name'] . "</option>";
            }

            echo "</select>";
            echo "</div>";
            echo "</form>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </div>

        </main>
        <!-- End Main -->
    </div>
    <!-- Scripts -->
    
<script src="myScript.js"></script> <!-- Link to your JavaScript file -->

    <!-- ApexCharts -->
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>
        <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>