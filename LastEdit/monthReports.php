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
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Default query for the current month
    $sql = "SELECT (select username from driver_details dd Where dd.id = driver_id) username, 
                   SUM(CASE WHEN DAY(pickup_date) = 1 THEN payment ELSE 0 END) AS '01',
                   SUM(CASE WHEN DAY(pickup_date) = 2 THEN payment ELSE 0 END) AS '02',
                   SUM(CASE WHEN DAY(pickup_date) = 3 THEN payment ELSE 0 END) AS '03',
                   SUM(CASE WHEN DAY(pickup_date) = 4 THEN payment ELSE 0 END) AS '04',
                   SUM(CASE WHEN DAY(pickup_date) = 5 THEN payment ELSE 0 END) AS '05',
                   SUM(CASE WHEN DAY(pickup_date) = 6 THEN payment ELSE 0 END) AS '06',
                   SUM(CASE WHEN DAY(pickup_date) = 7 THEN payment ELSE 0 END) AS '07',
                   SUM(CASE WHEN DAY(pickup_date) = 8 THEN payment ELSE 0 END) AS '08',
                   SUM(CASE WHEN DAY(pickup_date) = 9 THEN payment ELSE 0 END) AS '09',
                   SUM(CASE WHEN DAY(pickup_date) = 10 THEN payment ELSE 0 END) AS '10',
                   SUM(CASE WHEN DAY(pickup_date) = 11 THEN payment ELSE 0 END) AS '11',
                   SUM(CASE WHEN DAY(pickup_date) = 12 THEN payment ELSE 0 END) AS '12',
                   SUM(CASE WHEN DAY(pickup_date) = 13 THEN payment ELSE 0 END) AS '13',
                   SUM(CASE WHEN DAY(pickup_date) = 14 THEN payment ELSE 0 END) AS '14',
                   SUM(CASE WHEN DAY(pickup_date) = 15 THEN payment ELSE 0 END) AS '15',
                   SUM(CASE WHEN DAY(pickup_date) = 16 THEN payment ELSE 0 END) AS '16',
                   SUM(CASE WHEN DAY(pickup_date) = 17 THEN payment ELSE 0 END) AS '17',
                   SUM(CASE WHEN DAY(pickup_date) = 18 THEN payment ELSE 0 END) AS '18',
                   SUM(CASE WHEN DAY(pickup_date) = 19 THEN payment ELSE 0 END) AS '19',
                   SUM(CASE WHEN DAY(pickup_date) = 20 THEN payment ELSE 0 END) AS '20',
                   SUM(CASE WHEN DAY(pickup_date) = 21 THEN payment ELSE 0 END) AS '21',
                   SUM(CASE WHEN DAY(pickup_date) = 22 THEN payment ELSE 0 END) AS '22',
                   SUM(CASE WHEN DAY(pickup_date) = 23 THEN payment ELSE 0 END) AS '23',
                   SUM(CASE WHEN DAY(pickup_date) = 24 THEN payment ELSE 0 END) AS '24',
                   SUM(CASE WHEN DAY(pickup_date) = 25 THEN payment ELSE 0 END) AS '25',
                   SUM(CASE WHEN DAY(pickup_date) = 26 THEN payment ELSE 0 END) AS '26',
                   SUM(CASE WHEN DAY(pickup_date) = 27 THEN payment ELSE 0 END) AS '27',
                   SUM(CASE WHEN DAY(pickup_date) = 28 THEN payment ELSE 0 END) AS '28',
                   SUM(CASE WHEN DAY(pickup_date) = 29 THEN payment ELSE 0 END) AS '29',
                   SUM(CASE WHEN DAY(pickup_date) = 30 THEN payment ELSE 0 END) AS '30',
                   SUM(CASE WHEN DAY(pickup_date) = 31 THEN payment ELSE 0 END) AS '31',
                   SUM(CASE WHEN DAYOFWEEK(pickup_date) = 6 THEN payment ELSE 0 END) AS 'Friday',
                   SUM(payment) - SUM(CASE WHEN DAYOFWEEK(pickup_date) = 6 THEN payment ELSE 0 END) AS 'Without Friday'
            FROM booking_detail
            WHERE MONTH(pickup_date) = $currentMonth AND YEAR(pickup_date) = $currentYear
            GROUP BY driver_id";

    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }    
     elseif(isset($_POST['filterRides'])){
        $month = mysqli_real_escape_string($conn, $_POST['month']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);

        $sql = "SELECT (select username from driver_details dd Where dd.id = driver_id) username, 
                       SUM(CASE WHEN DAY(pickup_date) = 1 THEN payment ELSE 0 END) AS '01',
                       SUM(CASE WHEN DAY(pickup_date) = 2 THEN payment ELSE 0 END) AS '02',
                       SUM(CASE WHEN DAY(pickup_date) = 3 THEN payment ELSE 0 END) AS '03',
                       SUM(CASE WHEN DAY(pickup_date) = 4 THEN payment ELSE 0 END) AS '04',
                       SUM(CASE WHEN DAY(pickup_date) = 5 THEN payment ELSE 0 END) AS '05',
                       SUM(CASE WHEN DAY(pickup_date) = 6 THEN payment ELSE 0 END) AS '06',
                       SUM(CASE WHEN DAY(pickup_date) = 7 THEN payment ELSE 0 END) AS '07',
                       SUM(CASE WHEN DAY(pickup_date) = 8 THEN payment ELSE 0 END) AS '08',
                       SUM(CASE WHEN DAY(pickup_date) = 9 THEN payment ELSE 0 END) AS '09',
                       SUM(CASE WHEN DAY(pickup_date) = 10 THEN payment ELSE 0 END) AS '10',
                       SUM(CASE WHEN DAY(pickup_date) = 11 THEN payment ELSE 0 END) AS '11',
                       SUM(CASE WHEN DAY(pickup_date) = 12 THEN payment ELSE 0 END) AS '12',
                       SUM(CASE WHEN DAY(pickup_date) = 13 THEN payment ELSE 0 END) AS '13',
                       SUM(CASE WHEN DAY(pickup_date) = 14 THEN payment ELSE 0 END) AS '14',
                       SUM(CASE WHEN DAY(pickup_date) = 15 THEN payment ELSE 0 END) AS '15',
                       SUM(CASE WHEN DAY(pickup_date) = 16 THEN payment ELSE 0 END) AS '16',
                       SUM(CASE WHEN DAY(pickup_date) = 17 THEN payment ELSE 0 END) AS '17',
                       SUM(CASE WHEN DAY(pickup_date) = 18 THEN payment ELSE 0 END) AS '18',
                       SUM(CASE WHEN DAY(pickup_date) = 19 THEN payment ELSE 0 END) AS '19',
                       SUM(CASE WHEN DAY(pickup_date) = 20 THEN payment ELSE 0 END) AS '20',
                       SUM(CASE WHEN DAY(pickup_date) = 21 THEN payment ELSE 0 END) AS '21',
                       SUM(CASE WHEN DAY(pickup_date) = 22 THEN payment ELSE 0 END) AS '22',
                       SUM(CASE WHEN DAY(pickup_date) = 23 THEN payment ELSE 0 END) AS '23',
                       SUM(CASE WHEN DAY(pickup_date) = 24 THEN payment ELSE 0 END) AS '24',
                       SUM(CASE WHEN DAY(pickup_date) = 25 THEN payment ELSE 0 END) AS '25',
                       SUM(CASE WHEN DAY(pickup_date) = 26 THEN payment ELSE 0 END) AS '26',
                       SUM(CASE WHEN DAY(pickup_date) = 27 THEN payment ELSE 0 END) AS '27',
                       SUM(CASE WHEN DAY(pickup_date) = 28 THEN payment ELSE 0 END) AS '28',
                       SUM(CASE WHEN DAY(pickup_date) = 29 THEN payment ELSE 0 END) AS '29',
                       SUM(CASE WHEN DAY(pickup_date) = 30 THEN payment ELSE 0 END) AS '30',
                       SUM(CASE WHEN DAY(pickup_date) = 31 THEN payment ELSE 0 END) AS '31',
                       SUM(CASE WHEN DAYOFWEEK(pickup_date) = 6 THEN payment ELSE 0 END) AS 'Friday',
                       SUM(payment) - SUM(CASE WHEN DAYOFWEEK(pickup_date) = 6 THEN payment ELSE 0 END) AS 'Without Friday'
                FROM booking_detail
                WHERE MONTH(pickup_date) = $month AND YEAR(pickup_date) = $year
                GROUP BY driver_id";
    }

    $result = mysqli_query($conn, $sql);  
        // Fetch all rows into an array and store it in the session
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
$_SESSION['result_data'] = $data;

// Reset the result pointer back to the beginning
mysqli_data_seek($result, 0);
   
}
else{
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

<body>
<div class="grid-container">
        <!-- Header -->
        <?php
            include 'adminSideBar.php';
        ?>
            <div id="ReportToBePrinted" class="reportTable">
                <ul>
                <li id="PrintButton" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable()" class="active">
                            <i class='bx bx-printer'></i> Print PDF
                        </a>
                    </li>
                    <li id="PrintButton1" class="sidebar-list-item" style="list-style: none;">
                        <a href="javascript:void(0)" onclick="printTable1()" class="active">
                            <i class='bx bx-download'></i> Download Excel Sheet
                        </a>
                    </li>
                    <li style="list-style: none;">
                    <form id="filterForm" method="POST" action="monthReports.php">
           
                    <div class="form-group">
                    <label for="month">Month:</label>
                    <select name="month" id="month">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                        </div>
                        <div class="form-group">
                        <label for="year">Year:</label>
                            <select name="year" id="year">
                                <?php for ($i = date('Y'); $i >= 2000; $i--) : ?>
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    
            <div class="form-group">
                <br>
                <label></label>
                <button name="filterRides" type="submit">Filter</button>
            </div>
            

            
           
        </form>
                    </li>
                </ul>
                        
                            <?php if (mysqli_num_rows($result) > 0) : ?>
                <table class="table" style="text-align: center;" border="1">
                    <thead class="thead-dark">
                        <tr>
                            <th>Driver ID</th>
                            <?php for ($i = 1; $i <= 31; $i++) : ?>
                                <th><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></th>
                            <?php endfor; ?>
                            <th>Friday</th>
                            <th>Without Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $row['username']; ?></td>
                                <?php for ($i = 1; $i <= 31; $i++) : ?>
                                    <td><?= $row[str_pad($i, 2, '0', STR_PAD_LEFT)] ?: 0; ?></td>
                                <?php endfor; ?>
                                <td><?= $row['Friday']; ?></td>
                                <td><?= $row['Without Friday']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No records found for the selected month and year.</p>
            <?php endif; ?>
    
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
            newWin.document.write('<html><head><title>Print</title></head><body><style>@media print { table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;} td[style*="background-color:red"] {background-color: red !important; color: white;} td[style*="background-color:yellow"] {background-color: yellow !important; color: black;} td[style*="background-color:green"] {background-color: green !important; color: white;}}</style>' + divToPrint + '</body></html>');
            newWin.document.close();
            newWin.print();
            setTimeout(function () {
                newWin.print();
                newWin.close();
            }, 1000);
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
   

    <script>
    function printTable1() {
        // Create a hidden form to submit to the PHP script
        var form = document.createElement('form');
        form.action = 'download_csv.php'; // PHP script to handle the download
        form.method = 'POST';

        // Append the form to the body and submit it
        document.body.appendChild(form);
        form.submit();

        // Remove the form after submission
        document.body.removeChild(form);
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