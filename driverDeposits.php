<?php
include('connect.php');
session_start();
if ($_SESSION['loggedin'] == true) {
    $did = $_SESSION['id'];
    $sql3 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status = 'Completed'";
    $result3 = mysqli_query($conn, $sql3);
    $numOfCompletedRides = mysqli_num_rows($result3);

    $sql1 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND ride_status != 'Completed'";
    $result1 = mysqli_query($conn, $sql1);
    $numOfIncompleteRides = mysqli_num_rows($result1);

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
 
 // Update office_cash in booking_detail table
 $sqlUpdateOfficeCash = "UPDATE booking_detail SET office_cash = '$officeCash' WHERE driver_id = '$did'";
 $resultUpdateOfficeCash = mysqli_query($conn, $sqlUpdateOfficeCash);
 if (!$resultUpdateOfficeCash) {
     echo "Error updating office_cash: " . mysqli_error($conn);
     // Handle error as per your application's logic
 }

    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    }  elseif (isset($_POST['add-expenses'])) {
        $fuel = 0;
        $salik = 0;
        $Office =0;
        $License = 0;
        $Bills = 0;
        $Staff = 0;
        $Salaries = 0;
        $Depreciation = 0;
        $reserve = 0;
        $rent = 0;
        $other = $_POST['amount'];
        $date = $_POST['date'];
        $description = $_POST['description'];
        $type = "Bank Deposit";
        $sum = $fuel + $salik + $Office + $License + $Bills + $Staff + $Salaries + $Depreciation + $reserve + $rent + $other;
        $sql = "INSERT INTO expenses_details (add_by, fuel, salik, Office, License, Bills, Staff, Salaries, Depreciation, reserve, rent, other, total, type, driver_id, date, description)
                VALUES ('Driver', '$fuel', '$salik', '$Office', '$License', '$Bills', '$Staff', '$Salaries', '$Depreciation', '$reserve', '$rent', '$other', '$sum', '$type', '$did', '$date', '$description')";
        $res = mysqli_query($conn, $sql);
        if ($res == true) {
            echo '<script type="text/javascript">alert("Submitted Successfully");
                  window.location.href = "driverdashboard.php";
                  </script>';

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
            echo '<script type="text/javascript">alert("Not Submitted Please try again");
                  </script>';
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
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 700px;
            align-items: start;
            align-content: start;
            align-self: flex-start;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] ,input[type="date"],
        input[type="file"]
        {
            width: 95%;
            padding: 10px;
             border-radius: 5px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
select{
    width: 95%;
            padding: 10px;
             border-radius: 5px;
        
}
        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }

        fieldset {
            border: 2px solid #ccc;
            border-radius: 5px;
            padding: 13px;
            margin-bottom: 20px;
        }

        legend {
            padding: 0 10px;
            font-weight: bold;
        }
    </style>
    </style>
    <?php
            include 'adminAddStyles.php';
?>
    
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
            width: 80%; /* Adjusted width for 4 cards in a row */
            height: 100px;
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

<body>
<div class="grid-container">
            

<?php
include 'driverSideBar.php';
?>


           <div id="container">
                <div class="container">
                    <h2>Add Deposits</h2>
                    <form id="driverForm" method="POST" enctype="multipart/form-data" action="add_payment_bydriver.php">
                    <div class="form-group">
                            
                        <div class="form-group">
                          <label for="year">Amount<span style="color: #f90d0d;">*</span> </label>
                          <input type="number" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                          <label for="date">Date<span style="color: #f90d0d;">*</span> </label>
                          <input type="date" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                          <label for="description">Description</label>
                          <input type="text" id="description" name="description">
                        </div>          
                        <label >* indicate required fileds</label>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="add-expenses" class="button">Submit</button>
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