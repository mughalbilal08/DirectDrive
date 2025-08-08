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
    $sql3 ="select * from booking_detail";
    $result3 = mysqli_query($conn,$sql3);
    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }elseif (isset($_GET['updateride'])) {
        $rideId = $_GET['id'];
        echo $rideId;
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
                </ul>
                <table title="DriverDetails" class="table" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Profile Picture</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Username</th>
                            <th scope="col">Passport Image</th>
                            <th scope="col">Visa Expiry</th>
                            <th scope="col">Visa Image</th>
                            <th scope="col">Id Card Expiry</th>
                            <th scope="col">Id Card Image</th>
                            <th scope="col">Insurance Expiry</th>
                            <th scope="col">Insurance Image</th>
                            <th scope="col">Beneficery</th>
                            <th scope="col">Bank</th>
                            <th scope="col">Branch</th>
                            <th scope="col">IBAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          $sql4 = "SELECT * FROM `staff_details`";
                          $result4 = mysqli_query($conn,$sql4);
                       while($row =mysqli_fetch_assoc($result4)){
                        // Fetch profile picture path from database
                            $profilePicturePath = $row['profile_picture'];
                            $passportImagePath = $row['PassportNum'];
                            $visaImagePath = $row['visaNum'];
                            $idCardImagePath = $row['IDNum'];
                            $insuranceImagePath = $row['InNumber'];
                            
                        ?>
                        <tr>

                            <td scope="row">
                                <a href="<?= $profilePicturePath ?>" target="_blank">
                                    <img src="<?= $profilePicturePath ?>" alt="Profile Picture" width="50" height="50">
                                </a>
                                
                            </td>
                           
                            <td scope="row"><?php echo $row['name']; ?></td>
                            <td scope="row"><?php echo $row['phone']; ?></td>
                            <td scope="row"><?php echo $row['username']; ?></td>
                            <td scope="row"><?php echo $row['PassportEDate']; ?></td>
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
                            <td scope="row"><?php echo $row['IDEDate']; ?></td>
                            <td scope="row">
                                <a href="<?= $idCardImagePath ?>" target="_blank">
                                    <img src="<?= $idCardImagePath ?>" alt="ID Card Picture" width="50" height="50">
                                </a>
                            </td>
                            
                            <td scope="row"><?php echo $row['PassportEDate']; ?></td>
                            <td scope="row">
                                <a href="<?= $insuranceImagePath ?>" target="_blank">
                                    <img src="<?= $insuranceImagePath ?>" alt="Insurance Picture" width="50" height="50">
                                </a>
                            </td>
                            <td scope="row"><?php echo $row['benefiecery_name']; ?></td>
                            <td scope="row"><?php echo $row['bankname']; ?></td>
                            <td scope="row"><?php echo $row['branchname']; ?></td>
                         
                            
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
            newWin.document.write('<html><head><title>Print</title><style>table {width: 100%; border-collapse: collapse; margin-top: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: center;} thead th {background-color: #f2f2f2;}</style></head><body>' + divToPrint + '</body></html>');
            newWin.document.close();
            newWin.print();
            setTimeout(function () { newWin.close(); }, 10);
        }
    </script>
</body>

</html>