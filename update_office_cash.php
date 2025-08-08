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
            $cash_query = "SELECT office_cash, Name FROM booking_detail JOIN driver_details ON booking_detail.driver_id = driver_details.id WHERE booking_detail.driver_id = '$driver_id'";
            $cash_result = mysqli_query($conn, $cash_query);

            if ($cash_result && mysqli_num_rows($cash_result) > 0) {
                $cash_row = mysqli_fetch_assoc($cash_result);
                $office_cash = $cash_row['office_cash'];
                $driver_name = $cash_row['Name'];
                echo "<div class='alert alert-info mt-3'>Office Cash for Selected Driver $driver_name: AED$office_cash</div>";

                // Display form to update office_cash
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='driver_id' value='$driver_id'>";
                echo "<input type='hidden' name='driver_name' value='$driver_name'>";
                echo "<div class='form-group mt-3'>";
                echo "<label for='value_added'>Enter Value to Subtract:</label>";
                echo "<input type='number' class='form-control' id='value_added' name='value_added' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary' name='update_office_cash'>Update Office Cash</button>";
                echo "</form>";
            } else {
                echo "<div class='alert alert-warning mt-3'>No office cash details found for the selected driver.</div>";
            }

            // Debugging information
            if (!$cash_result) {
                echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
            }
        }

        // Handle office cash update
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_office_cash'])) {
            $driver_id = mysqli_real_escape_string($conn, $_POST['driver_id']);
            $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
            $value_added = mysqli_real_escape_string($conn, $_POST['value_added']);

            // Fetch current office_cash
            $fetch_query = "SELECT office_cash FROM booking_detail WHERE driver_id = '$driver_id'";
            $fetch_result = mysqli_query($conn, $fetch_query);

            if ($fetch_result && mysqli_num_rows($fetch_result) > 0) {
                $fetch_row = mysqli_fetch_assoc($fetch_result);
                $current_office_cash = $fetch_row['office_cash'];

                // Calculate new office_cash
                $new_office_cash = $current_office_cash - $value_added;

                // Update office_cash in booking_detail table
                $update_query = "UPDATE booking_detail SET office_cash = '$new_office_cash' WHERE driver_id = '$driver_id'";
                $update_result = mysqli_query($conn, $update_query);

                if ($update_result) {
                    echo "<div class='alert alert-success mt-3'>Office Cash updated successfully!</div>";

                    // Insert into new table for audit trail
                    $insert_query = "INSERT INTO office_cash_updates (driver_id, driver_name, updated_cash) VALUES ('$driver_id', '$driver_name', '$value_added')";
                    $insert_result = mysqli_query($conn, $insert_query);

                    if (!$insert_result) {
                        echo "<div class='alert alert-danger'>Error inserting into audit table: " . mysqli_error($conn) . "</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger mt-3'>Error updating office_cash: " . mysqli_error($conn) . "</div>";
                }
            } else {
                echo "<div class='alert alert-warning mt-3'>No office cash details found for the selected driver.</div>";
            }

            // Debugging information
            if (!$fetch_result) {
                echo "<div class='alert alert-danger'>Error fetching office_cash: " . mysqli_error($conn) . "</div>";
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