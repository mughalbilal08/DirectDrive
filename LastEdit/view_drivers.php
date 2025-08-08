<?php
include('connect.php');
session_start();
if($_SESSION['loggedin'] == true){
    
    $sql0 = "SELECT * FROM driver_details WHERE role ='Driver'";
    $result0 = mysqli_query($conn, $sql0);
    $numberofDrivers = mysqli_num_rows($result0);
    
    $sql1 = "SELECT * FROM user_accounts WHERE role ='customer'";
    $result1 = mysqli_query($conn, $sql1);
    $numberofCustomers = mysqli_num_rows($result1);
    
    $sql2 = "SELECT SUM(total) AS total FROM expenses_details;";
    $result2 = mysqli_query($conn, $sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];
    
    $sql3 = "SELECT * FROM booking_detail";
    $result3 = mysqli_query($conn, $sql3);

    // Function to calculate days difference
    function daysDifference($date1, $date2) {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $difference = $date1 - $date2;
        return round($difference / (60 * 60 * 24));
    }

    // Function to determine color based on days left
    function getColor($daysLeft) {
        if ($daysLeft <= 15) {
            return 'red';
        } elseif ($daysLeft <= 100) {
            return 'yellow';
        } else {
            return 'green';
        }
    }

    if(isset($_GET['logout'])){
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif (isset($_GET['updateride'])) {
        $rideId = $_GET['id'];
        echo $rideId;
    }
} else {
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
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
                </ul>
                <table title="DriverDetails" class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Profile Picture</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Username</th>
                            <th scope="col">Vehicle Number</th>
                            <th scope="col">Passport Expiry</th>
                            <th scope="col">Passport Image</th>
                            <th scope="col">Visa Expiry</th>
                            <th scope="col">Visa Image</th>
                            <th scope="col">Id Card Expiry</th>
                            <th scope="col">Id Card Image</th>
                            <th scope="col">RTA Expiry</th>
                            <th scope="col">RTA Image</th>
                            <th scope="col">License Expiry</th>
                            <th scope="col">License Image</th>
                            <th scope="col">Insurance Expiry</th>
                            <th scope="col">Insurance Image</th>
                            <th scope="col">Beneficiary</th>
                            <th scope="col">Bank</th>
                            <th scope="col">Branch</th>
                            <th scope="col">IBAN</th>
                            <th scope="col">Passport Days Left</th>
                            <th scope="col">Visa Days Left</th>
                            <th scope="col">ID Card Days Left</th>
                            <th scope="col">RTA Days Left</th>
                            <th scope="col">License Days Left</th>
                            <th scope="col">Actions</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$sql4 = "SELECT * FROM driver_details";
$result4 = mysqli_query($conn, $sql4);
while ($row = mysqli_fetch_assoc($result4)) {
    // Calculate days difference for each expiry date
    $passportDaysLeft = daysDifference($row['passportEDate'], date('Y-m-d'));
    $visaDaysLeft = daysDifference($row['VisaEDate'], date('Y-m-d'));
    $idCardDaysLeft = daysDifference($row['IdEDate'], date('Y-m-d'));
    $rtaDaysLeft = daysDifference($row['RtaEDate'], date('Y-m-d'));
    $licenseDaysLeft = daysDifference($row['DLEDate'], date('Y-m-d'));

    // Fetch profile picture path from database
    $profilePicturePath = $row['profile_img'];
    $passportImagePath = $row['passportnum'];
    $visaImagePath = $row['Visanum'];
    $idCardImagePath = $row['IdNum'];
    $rtaCardImagePath = $row['RtaNum'];
    $drivingLicenseImagePath = $row['DLicenseNum'];
    $insuranceImagePath = $row['InsuranceNo'];
    
  


    // Display each driver's details in a table row
?>
    <tr>

        <td scope="row">
            <a href="<?= $profilePicturePath ?>" target="_blank">
                <img src="<?= $profilePicturePath ?>" alt="Profile Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['Name']; ?></td>
        <td scope="row"><?php echo $row['phone']; ?></td>
        <td scope="row"><?php echo $row['username']; ?></td>
        <?php
        // Assuming you have a connection to your database in $conn
        $vehicleId = $row['vehicleId'];

        // Query to fetch the number from vehicle_details table
        $sql = "SELECT Number FROM vehicle_details WHERE id = '$vehicleId'";
        $result = mysqli_query($conn, $sql);

        // Fetch the result
        $vehicleNumber = "";
        if ($result && mysqli_num_rows($result) > 0) {
            $vehicleRow = mysqli_fetch_assoc($result);
            $vehicleNumber = $vehicleRow['Number'];
        }
        ?>

        <td scope="row"><?php echo $vehicleNumber; ?></td>
        <td scope="row"><?php echo $row['passportEDate']; ?></td>
        <td scope="row">
            <a href="<?= $passportImagePath ?>" target="_blank">
                <img src="<?= $passportImagePath ?>" alt="Passport Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['VisaEDate']; ?></td>
        <td scope="row">
            <a href="<?= $visaImagePath ?>" target="_blank">
                <img src="<?= $visaImagePath ?>" alt="Visa Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['IdEDate']; ?></td>
        <td scope="row">
            <a href="<?= $idCardImagePath ?>" target="_blank">
                <img src="<?= $idCardImagePath ?>" alt="ID Card Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['RtaEDate']; ?></td>
        <td scope="row">
            <a href="<?= $rtaCardImagePath ?>" target="_blank">
                <img src="<?= $rtaCardImagePath ?>" alt="RTA Card Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['DLEDate']; ?></td>
        <td scope="row">
            <a href="<?= $drivingLicenseImagePath ?>" target="_blank">
                <img src="<?= $drivingLicenseImagePath ?>" alt="Driver License Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['InsuranceEDate']; ?></td>
        <td scope="row">
            <a href="<?= $insuranceImagePath ?>" target="_blank">
                <img src="<?= $insuranceImagePath ?>" alt="Insurance Picture" width="50" height="50">
            </a>
        </td>
        <td scope="row"><?php echo $row['Beneficery_name']; ?></td>
        <td scope="row"><?php echo $row['bankname']; ?></td>
        <td scope="row"><?php echo $row['branchName']; ?></td>
        <td scope="row"><?php echo $row['Iban']; ?></td>
        <td scope="row" style="background-color: <?php echo getColor($passportDaysLeft); ?>; color: black;"><?php echo $passportDaysLeft; ?></td>
        <td scope="row" style="background-color: <?php echo getColor($visaDaysLeft); ?>; color: black;"><?php echo $visaDaysLeft; ?></td>
        <td scope="row" style="background-color: <?php echo getColor($idCardDaysLeft); ?>; color: black;"><?php echo $idCardDaysLeft; ?></td>
        <td scope="row" style="background-color: <?php echo getColor($rtaDaysLeft); ?>; color: black;"><?php echo $rtaDaysLeft; ?></td>
        <td scope="row" style="background-color: <?php echo getColor($licenseDaysLeft); ?>; color: black;"><?php echo $licenseDaysLeft; ?></td>
        <td scope="row"><a href="updatedriver.php?id=<?php echo $row['id']; ?>" style="color: white;">Update</a></td>
        <td scope="row"><a href="delete_driver.php?id=<?php echo $row['id']; ?>" style="color: white;">Delete</a></td>
    </tr>
<?php
}
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
            newWin.document.write('<html><head><title>Print</title></head><body><style>@media print { table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;} td[style*="background-color:red"] {background-color: red !important; color: white;} td[style*="background-color:yellow"] {background-color: yellow !important; color: black;} td[style*="background-color:green"] {background-color: green !important; color: white;}}</style>' + divToPrint + '</body></html>');
            newWin.document.close();
            // Delay the printing process
            setTimeout(function () {
                newWin.print();
                newWin.close();
            }, 1000); // Adjust the delay time (in milliseconds) as needed
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
