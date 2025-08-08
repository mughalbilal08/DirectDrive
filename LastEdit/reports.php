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
    $sql3 ="SELECT * FROM booking_detail b JOIN notifications n ON b.id = n.rideId WHERE b.role = 'customer'";
    $result3 = mysqli_query($conn,$sql3);
    $sqlCustomers = "SELECT DISTINCT customer_name FROM booking_detail where role = 'customer';";
    $resultCustomers = mysqli_query($conn, $sqlCustomers);


    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif(isset($_POST['filterRides'])){
        $status = $_POST['ridestatus'];
        $customerName = $_POST['customerName'];
        
        $sql4 ="SELECT * from booking_detail b JOIN notifications n ON b.id = n.rideId where b.role = 'customer'";
       
        if($customerName !='All')
        {
            $sql4 .="AND customer_name ='$customerName'";
        }
        
        if($status!='All' )
        {
            $sql4 .="AND status ='$status'";
           
        }

        $result3 = mysqli_query($conn,$sql4);
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
            include 'adminSideBar.php';
?>

            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i>Print
                        </a>
                    </li>
                    <li style="list-style: none;">
                    <form id="filterForm" method="POST" action="reports.php">
            <div class="form-group">
                <label for="preference">Preference:</label>
                <select id="ridestatus" name="ridestatus">
                        <option value="All">All</option>
                        <option value="Declined">Rides Declined</option>
                        <option value="Pending">Rides Pending</option>
                        <option value="Accepted">Rides In Progress</option>
                        <option value="Completed">Rides Completed</option>
                    </select>
            </div>
            

            <div class="form-group">
                <label for="customerName">Customers</label>
                    <select id="customerName" name="customerName">
                        <option value="All">All</option>
                        <?php
                            while ($row = mysqli_fetch_assoc($resultCustomers)) {
                                echo '<option value="' . $row['customer_name'] . '">' . $row['customer_name'] . '</option>';
                            }
                        ?>
                    </select>
            </div>

            
            <div class="form-group">
            <label></label>
            <br>
                <button name="filterRides" type="submit">Filter</button>
            </div>
        </form>
                    </li>
                </ul>
                <table class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Passengers</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Booking Date</th>
                            <th scope="col">Booking Time</th>
                            <th scope="col">Ride Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       while($row =mysqli_fetch_assoc($result3))
                        echo'
                        <tr>
                            <td scope="row">'.$row['customer_name'].'</td>
                            <td scope="row">'.$row['NumOfPassengers'].'</td>
                            <td scope="row">'.$row['pickup_location'].'</td>
                            <td scope="row">'.$row['drop_location'].'</td>
                            <td scope="row">'.$row['pickup_date'].'</td>
                            <td scope="row">'.$row['pickup_time'].'</td>
                            <td scope="row">'.$row['status'].'</td>
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

        document.getElementById('addOptionButton').addEventListener('click', function() {
        // Create a new option element
        var newOption = document.createElement('option');
        newOption.value = 'NewOptionValue'; // Set the value attribute
        newOption.text = 'New Option'; // Set the text content

        // Add the new option to the select element
        var selectElement = document.getElementById('ridestatus');
        selectElement.appendChild(newOption);
        });
    </script>
   
    
</body>

</html>