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

    $sql6 = "select * from booking_detail where driver_id ='$did'";
    $result6 = mysqli_query($conn,$sql6);

    // Filter by date if date is set
    if(isset($_POST['filterRides']) && isset($_POST['start_date']) && isset($_POST['end_date']) && $_POST['start_date'] != '' && $_POST['end_date'] != ''){
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $sql6 = "SELECT * FROM booking_detail WHERE driver_id = '$did' AND pickup_date BETWEEN '$start_date' AND '$end_date'";
        $result6 = mysqli_query($conn, $sql6);
    }
    // Fetch total income
    $sql8 = "select SUM(payment) AS total_income from booking_detail where driver_id ='$did' and ride_status = 'Completed'";
    $result8 = mysqli_query($conn, $sql8);
    $totalIncome = mysqli_fetch_assoc($result8)['total_income'];

    // Fetch total expenses
    $sql7 = "select SUM(total) AS total_expense from expenses_details where driver_id ='$did'";
    $result7 = mysqli_query($conn, $sql7);
    $totalExpense = mysqli_fetch_assoc($result7)['total_expense'];


    
    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }elseif(isset($_GET['updateridstart'])){
        echo"start";
        $rid = $_GET['id'];
        $sql4 = "Update booking_detail set ride_status='Started' where id = '$rid' ";
        echo $sql4;
       $result4 = mysqli_query($conn,$sql4);
       if($result4==true){
           echo '<script type="text/javascript">alert("Updated Successfully");
           window.location.href = "driverdashboard.php";
           </script>';
       }

    }elseif(isset($_GET['updatecomplete'])){
        echo"complete";
        $rid = $_GET['id'];
        $sql4 = "Update booking_detail set ride_status='Completed' where id = '$rid' ";
        echo $sql4;
       $result4 = mysqli_query($conn,$sql4);
       if($result4==true){
           echo '<script type="text/javascript">alert("Updated Successfully");
           window.location.href = "driverdashboard.php";
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
    <title>Driver Dashboard</title>

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

h2 {
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 20px;
}

.form-group {
    margin: 10px;
}

form select, form button {
    padding: 10px;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}
@media (max-width: 600px) {
    form {
        flex-direction: column;
    }

    .form-group {
        width: 100%;
    }
}

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
    <?php
            include 'cardStyles.php';
?>

</head>

<body onload="renderChart()">
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
    <form method="post" action="">
       

        <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date">
            </div>

        <div class="form-group">
            <br>
            <label></label>
            <button type="submit" name="filterRides">Filter</button>
        </div>
    </form>
    <table class="table" style="text-align: center;">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Ride ID</th>
                <th scope="col">Passengers</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">Booking Date</th>
                <th scope="col">Mode</th>
                <th scope="col">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
           while($row =mysqli_fetch_assoc($result6)){
             echo'
            <tr>
                <td scope="row">'.$row['id'].'</td>
                <td scope="row">'.$row['customer_name'].'</td>
                <td scope="row">'.$row['pickup_location'].'</td>
                <td scope="row">'.$row['drop_location'].'</td>
                <td scope="row">'.$row['pickup_date'].'</td>
                <td scope="row">'.$row['mode'].'</td>
                <td scope="row">'.$row['payment'].'</td>
            </tr>
            ';
           }

           $sql10 ="select SUM(payment)  AS  total from booking_detail where driver_id  = '$did'";
           if( isset($_POST['start_date']) && isset($_POST['end_date']) && $_POST['start_date'] != '' && $_POST['end_date'] != ''){
            $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
            $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
            $sql10 .= " AND pickup_date BETWEEN '$start_date' AND '$end_date'";;
        }
          
           $result10 = mysqli_query($conn,$sql10);
       $total= mysqli_fetch_assoc($result10);
           echo'
           <tr>
               <td scope="row">Total</td> 
               <td scope="row">'.$total['total'].'</td>
           </tr>
           ';
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
