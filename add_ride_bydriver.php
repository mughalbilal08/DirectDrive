<?php
include ('connect.php');
session_start();

if ($_SESSION['loggedin'] == true) {
    $did = $_SESSION['id'];
    $sql3 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status = 'Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);

    $sql1 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status != 'Completed'";
    $result1 = mysqli_query($conn, $sql1);
    $numOfIncompleteRides = mysqli_num_rows($result1);

    $sql12 = "SELECT vehicleId FROM driver_details WHERE id = '$did'";
    $result12 = mysqli_query($conn, $sql12);
    
    if ($result12 && mysqli_num_rows($result12) > 0) {
        $row = mysqli_fetch_assoc($result12);
        $vehicleId = $row['vehicleId'];
    } else {
        $vehicleId = null; // or handle the case where no vehicle ID is found
    }

    $sql4 = "SELECT * FROM booking_detail WHERE driver_id = '$did'";
    $result4 = mysqli_query($conn, $sql4);

    // Fetch total income
    $sql8 = "SELECT SUM(payment) AS total_income FROM booking_detail WHERE driver_id = '$did' AND ride_status = 'Completed'";
    $result8 = mysqli_query($conn, $sql8);
    $totalIncome = mysqli_fetch_assoc($result8)['total_income'];

    // Fetch total expenses
    $sql7 = "SELECT SUM(total) AS total_expense FROM expenses_details WHERE driver_id = '$did'";
    $result7 = mysqli_query($conn, $sql7);
    $totalExpense = mysqli_fetch_assoc($result7)['total_expense'];




    // Initialize variables
    $currentOfficeCash = 0;
    $totalUpdateCash = 0;
    $totalCash = 0;
    $totalPersonalCash = 0;
    $totalExpense = 0;

    // Fetch sum of update_cash from office_cash_updates table
    $sqlSumUpdateCash = "SELECT SUM(updated_cash) AS total_update_cash FROM office_cash_updates WHERE driver_id = '$did'";
    $resultSumUpdateCash = mysqli_query($conn, $sqlSumUpdateCash);

    if ($resultSumUpdateCash) {
        $totalUpdateCashRow = mysqli_fetch_assoc($resultSumUpdateCash);
        $totalUpdateCash = isset($totalUpdateCashRow['total_update_cash']) ? (float) $totalUpdateCashRow['total_update_cash'] : 0;
    } else {
        echo "Error fetching total update_cash: " . mysqli_error($conn);
    }

    // Fetch total Cash payments
    $sqlTotalCash = "SELECT SUM(payment) AS total_cash FROM booking_detail WHERE driver_id = '$did' AND mode = 'Cash'";
    $resultTotalCash = mysqli_query($conn, $sqlTotalCash);

    if ($resultTotalCash) {
        $totalCashRow = mysqli_fetch_assoc($resultTotalCash);
        $totalCash = isset($totalCashRow['total_cash']) ? (float) $totalCashRow['total_cash'] : 0;
    } else {
        echo "Error fetching total cash payments: " . mysqli_error($conn);
    }

    // Fetch total Personal Cash payments
    $sqlTotalPersonalCash = "SELECT SUM(payment) AS total_personal_cash FROM booking_detail WHERE driver_id = '$did' AND mode = 'Personal Cash'";
    $resultTotalPersonalCash = mysqli_query($conn, $sqlTotalPersonalCash);

    if ($resultTotalPersonalCash) {
        $totalPersonalCashRow = mysqli_fetch_assoc($resultTotalPersonalCash);
        $totalPersonalCash = isset($totalPersonalCashRow['total_personal_cash']) ? (float) $totalPersonalCashRow['total_personal_cash'] : 0;
    } else {
        echo "Error fetching total personal cash payments: " . mysqli_error($conn);
    }

    // Query to get the total expenses
    $sqlTotalExpense = "SELECT SUM(total) AS total_expense FROM expenses_details WHERE driver_id = '$did'";
    $resultTotalExpense = mysqli_query($conn, $sqlTotalExpense);

    if ($resultTotalExpense) {
        $totalExpenseRow = mysqli_fetch_assoc($resultTotalExpense);
        $totalExpense = isset($totalExpenseRow['total_expense']) ? (float) $totalExpenseRow['total_expense'] : 0;
    } else {
        echo "Error fetching total expenses: " . mysqli_error($conn);
    }

    // Calculate Office Cash
    $officeCash = ($totalCash + $totalPersonalCash) - ($totalExpense + $totalUpdateCash);

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif (isset($_POST['add-ride'])) {

        if ($vehicleId) {
        $customer_id = $_SESSION['id'];
        $customer_name = $_POST['pname'];
        $pickup_location = $_POST['pickup-location'];
        $pickup_date = $_POST['pickup-date'];
        $pickup_time = $_POST['pickup-time'];
        $NumOfPassengers = $_POST['passengers'];
        $description = $_POST['description'];
        $drop_location = $_POST['drop-location'];
        $Payment = $_POST['payment'];
        $ride_status = 'Completed';
        $mode = $_POST['PMode'];
        $nType = $_POST['nType'];
        $rideDistance = $_POST['rideDistance'];
        $rideMinutes = $_POST['rideMinutes'];
        $rideHours = $_POST['rideHours'];

        $rideTime = $rideHours . ':' .$rideMinutes;

        // Handle file upload
        $targetFilePath = '';
        if (isset($_FILES["ridepicture"]) && $_FILES["ridepicture"]["error"] == 0) {
            $targetDir = "ridedata/";
            $fileName = basename($_FILES["ridepicture"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            if (in_array($fileType, array('jpg', 'png'))) {
                if (!move_uploaded_file($_FILES["ridepicture"]["tmp_name"], $targetFilePath)) {
                    echo '<script type="text/javascript">alert("File upload failed, please try again.");</script>';
                }
            } else {
                echo '<script type="text/javascript">alert("Only JPG and PNG files are allowed.");</script>';
            }
        }

        // Insert new booking record
        $sql = "INSERT INTO `booking_detail` (`customer_id`, `customer_name`, `pickup_location`, `pickup_date`, `pickup_time`, `NumOfPassengers`, `description`, `drop_location`, `ride_status`, `payment`,`vehicle`, `driver_id`, `mode`, `ride_picture` ,`rideDistance`,`driverTime`, `role`, `newtype` ) VALUES ('$customer_id', '$customer_name', '$pickup_location', '$pickup_date', '$pickup_time', '$NumOfPassengers', '$description', '$drop_location', '$ride_status', '$Payment',(SELECT Number FROM vehicle_details v  WHERE v.id = '$vehicleId'), '$did', '$mode', '$targetFilePath','$rideDistance','$rideTime', 'driver', '$nType' )";
        $res = mysqli_query($conn, $sql);

        if ($res) {
            echo '<script type="text/javascript">alert("Submitted Successfully"); window.location.href = "driverdashboard.php";</script>';
            

            // Recalculate Office Cash after new booking
            $sqlRecalculate = "SELECT SUM(payment) AS total_cash FROM booking_detail WHERE driver_id = '$did' AND mode = 'Cash'";
            $resultRecalculate = mysqli_query($conn, $sqlRecalculate);
            $totalCashRecalculate = mysqli_fetch_assoc($resultRecalculate)['total_cash'];

            if (!$totalCashRecalculate) {
                $totalCashRecalculate = 0; // If no cash payments, set to 0
            }

            $sqlRecalculatePersonalCash = "SELECT SUM(payment) AS total_personal_cash FROM booking_detail WHERE driver_id = '$did' AND mode = 'Personal Cash'";
            $resultRecalculatePersonalCash = mysqli_query($conn, $sqlRecalculatePersonalCash);
            $totalPersonalCashRecalculate = mysqli_fetch_assoc($resultRecalculatePersonalCash)['total_personal_cash'];

            if (!$totalPersonalCashRecalculate) {
                $totalPersonalCashRecalculate = 0; // If no personal cash payments, set to 0
            }

            // Update Office Cash after new booking
            $newOfficeCash = ($totalCashRecalculate + $totalPersonalCashRecalculate) - ($totalExpense + $totalUpdateCash);

            $sqlUpdateOfficeCash = "UPDATE booking_detail SET office_cash = '$newOfficeCash' WHERE driver_id = '$did'";
            $resultUpdateOfficeCash = mysqli_query($conn, $sqlUpdateOfficeCash);

            if (!$resultUpdateOfficeCash) {
                echo "Error updating office_cash: " . mysqli_error($conn);
                // Handle error as per your application's logic
            } else {
                echo "office_cash updated successfully!";
            }
        } else {
            echo '<script type="text/javascript">alert("Not Submitted Please try again");</script>';
        }
    }
    else {
        echo '<script type="text/javascript">alert("No vehicle was assigned to you, you can not add a ride");</script>';
    }

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
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboardstyle.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #09715F;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        .form-group textarea {
            resize: vertical;
        }

        button {
            background-color: #e11c1c;
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #d71a1a;
        }

        @media (min-width: 600px) {
            form {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .form-group {
                flex: 1 1 48%;
                margin-right: 4%;
            }

            .form-group:nth-child(2n) {
                margin-right: 0;
            }

            .form-group.full-width {
                flex: 1 1 100%;
            }

            button {
                flex: 1 1 100%;
            }
        }
    </style>

    </style>

    
<style>
        .mainCards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .materialIconsOutlined {
            vertical-align: middle;
            line-height: 1px;
            font-size: 35px;
        }
        
        .myCard {
            width: 90%; /* Adjusted width for 4 cards in a row */
            height: 135px;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            text-decoration: none;
            color: black;
            position: relative;
            overflow: hidden;
            background-color: #1abc9c; /* Same color for all cards */
        }
        
        .cardInner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .cardInner > .materialIconsOutlined {
            font-size: 45px;
        }
        
        .card h1 {
            margin: 20px 0;
        }
        
        .card-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        
        .card-popup {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 10px 0;
            font-size: 16px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .myCard:hover .card-popup {
            opacity: 1;
        }
        
        .myCard:hover {
            opacity: 0.8;
            transform: translateY(-5px);
        }
    </style>

<style>
        /* Inline CSS for quick styling, move to styles.css if needed */
        .sidebar-section {
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
            color: black;
            background-color: #1abc9c;
            border-radius: 5px;
        }
        .sidebar-section:hover {
            background-color: #e0e0e0;
        }
        .sidebar-sublist {
            display: none; /* Initially hide all sections */
            padding-left: 20px;
        }
    </style>


    

</head>

<body onload="renderChart()">
<div class="grid-container">
            

<?php
include 'driverSideBar.php';
?>



            <div class="container">
                <h1>Add Ride Now!</h1>
                <form method="POST" enctype="multipart/form-data" action="add_ride_bydriver.php">
                    <div class="form-group">
                        <label for="pickup-date">Pick Up Date<span style="color: #f90d0d;">*</span></label>
                        <input type="date" id="pickup-date" name="pickup-date" required>
                    </div>
                    <div class="form-group">
                        <label for="pickup-time">Pick Up Time</label>
                        <input type="time" id="pickup-time" name="pickup-time">
                    </div>
                    <div class="form-group">
                        <label for="pickup-location">Pickup Location</label>
                        <input type="text" id="pickup-location" name="pickup-location" placeholder="Pickup Location">
                    </div>
                    <div class="form-group">
                        <label for="pickup-location">Name Of Passenger</label>
                        <input type="text" id="pname" name="pname" placeholder="Passenger name">
                    </div>
                    <div class="form-group">
                        <label for="passengers">No of Passengers</label>
                        <input type="number" id="passengers" name="passengers" placeholder="No of Passengers">
                    </div>
                    <div class="form-group">
                        <label for="drop-location">Drop Location</label>
                        <input type="text" id="drop-location" name="drop-location" placeholder="Drop Location">
                    </div>
                    <div class="form-group">
                        <label for="drop-location">Payment Mode<span style="color: #f90d0d;">*</span></label>
                        <select class="selector" name="PMode" id="PMode" onchange="toggleFileInput()" required>
                            <option value="">-------------</option>
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
                    <div class="form-group" id="file-input" style="display: none;">
                        <label class="control-label">Upload Picture</label>
                        <input type="file" name="ridepicture" class="form-control-file" accept=".jpg, .png">
                    </div>
                    <div class="form-group">
                        <label for="payment">Payment<span style="color: #f90d0d;">*</span></label>
                        <input type="number" id="payment" name="payment" placeholder="Ride Payment" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="drop-location">Type<span style="color: #f90d0d;">*</span></label>
                        <select class="selector" name="nType" id="nType" onchange="toggleFileInput()" required>
                            <option value="">-------------</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                   
                    <div class="form-group">
                        <label for="deposit">Ride Distance</label>
                        <input type="number" id="rideDistance" name="rideDistance" placeholder="Ride Ditance (KM)">
                    </div>
                    
                    <div class="form-group">
    <label for="rideHours">Ride Hours Taken</label>
    <input type="number" id="rideHours" name="rideHours" min="0" placeholder="Hours">

    <label for="rideMinutes">Ride Minutes Taken</label>
    <input type="number" id="rideMinutes" name="rideMinutes" min="0" max="59" placeholder="Minutes">
</div>
                    <button name="add-ride" type="submit">Book</button>
                </form>
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
        function toggleFileInput() {
            var paymentMode = document.getElementById('PMode').value;
            var fileInput = document.getElementById('file-input');
            if (paymentMode     == 'Network' || paymentMode     == 'Voucher') {
                fileInput.style.display = 'block';
            } else {
                fileInput.style.display = 'none';
            }
        }
    </script>
</body>

</html>