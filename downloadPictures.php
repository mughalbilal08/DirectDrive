

<?php
include('connect.php');
session_start();
if ($_SESSION['loggedin'] == true) {


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

    $sql3 = "SELECT username, vehicle, customer_name, newType, mode, IFNULL((payment), 0) AS total, pickup_date, rideDistance, driverTime, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND MONTH(pickup_date) = '$currentMonth' AND YEAR(pickup_date) = '$currentYear'";
    $result3 = mysqli_query($conn,$sql3);

    $sql11 ="SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND MONTH(pickup_date) = '$currentMonth' AND YEAR(pickup_date) = '$currentYear';";
    $result10 = mysqli_query($conn, $sql11);
    $total = mysqli_fetch_assoc($result10);
    


    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        session_unset();
        session_destroy();
        header("location:index.html");
        exit;
    }

    if (isset($_POST['filterRides'])) {
        $month = mysqli_real_escape_string($conn, $_POST['month']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        
        $sqlImages = "SELECT username, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE MONTH(pickup_date) = '$month' AND YEAR(pickup_date) = '$year'";
        $resultImages = mysqli_query($conn, $sqlImages);
        
        if (mysqli_num_rows($resultImages) > 0) {
            $baseDir = "downloads"; // Base directory for storing downloaded images
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0777, true); // Create base directory if not exists
            }
    
            while ($row = mysqli_fetch_assoc($resultImages)) {
                $driverId = $row['username'];
                $ridePicture = $row['ride_picture'];
                
                if (!empty($ridePicture)) {
                    $driverDir = $baseDir . '/' . $driverId;
                    if (!is_dir($driverDir)) {
                        mkdir($driverDir, 0777, true); // Create driver directory if not exists
                    }
    
                    $filename = basename($ridePicture);
                    $filePath = $driverDir . '/' . $filename;
                    file_put_contents($filePath, file_get_contents($ridePicture));
                }
            }
    
            // Compress all driver folders into a single ZIP file
            $zipFile = $month ."_". $year ."_". "images_Repair_the_file_to_access.zip";
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
                foreach ($iterator as $key => $value) {
                    if (!$value->isDir()) {
                        $filePath = $value->getPathname();
                        $relativePath = substr($filePath, strlen($baseDir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
    
                // Reopen the ZIP file to ensure it is properly finalized and repaired
                $zip->open($zipFile);
                $zip->close();
    
                // Set headers to download the ZIP file
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFile . '"');
                header('Content-Length: ' . filesize($zipFile));
    
                readfile($zipFile);
    
                // Clean up
                unlink($zipFile); // Delete the ZIP file after download
                array_map('unlink', glob("$baseDir/*/*.*")); // Delete individual images
                array_map('rmdir', glob("$baseDir/*")); // Remove driver directories
                rmdir($baseDir); // Remove the base directory
                exit;
            }
        } else {
            echo "No images found for the selected month and year.";
        }
    }
    elseif(isset($_POST['filterRides1'])){
       
        $month = mysqli_real_escape_string($conn, $_POST['month']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
          
        
    
             
       
        $sql10 ="SELECT IFNULL(SUM(payment), 0) AS total FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE 1=1 AND MONTH(pickup_date) = '$month' AND YEAR(pickup_date) = $year";
        $sql4 = "SELECT username, vehicle, customer_name, newType, mode, IFNULL((payment), 0) AS total, pickup_date, rideDistance, driverTime, ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id  WHERE 1=1 AND MONTH(pickup_date) = '$month' AND YEAR(pickup_date) = '$year'";
        
        
        $result3 = mysqli_query($conn,$sql4);
        $result10 = mysqli_query($conn, $sql10);
        $total = mysqli_fetch_assoc($result10);
     }
     elseif (isset($_POST['deleteRides'])) {
        $month = mysqli_real_escape_string($conn, $_POST['month']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $sqlDeleteImages = "SELECT ride_picture FROM booking_detail b JOIN driver_details d ON b.driver_id = d.id WHERE MONTH(pickup_date) = '$month' AND YEAR(pickup_date) = '$year'";
        $resultDeleteImages = mysqli_query($conn, $sqlDeleteImages);
    
        if (mysqli_num_rows($resultDeleteImages) > 0) {
            while ($row = mysqli_fetch_assoc($resultDeleteImages)) {
                $ridePicture = $row['ride_picture'];
    
                if (!empty($ridePicture)) {
                    // Determine the full path of the file on the server
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $ridePicture;
                    
                    // Ensure the correct path separator (forward slash for UNIX-based systems like GoDaddy)
                    $filePath = str_replace('\\', '/', $filePath);
                    
                    if (file_exists($filePath)) {
                        // Delete the file from the server
                        if (unlink($filePath)) {
                            echo "Deleted: $filePath<br>";
                        } else {
                            echo "Failed to delete: $filePath<br>";
                        }
                    } else {
                        echo "File not found: $filePath<br>";
                    }
                }
            }
            echo "Deletion process completed.";
        } else {
            echo "No images found for the selected month and year.";
        }
    }
    

    
    
} 

else {
    header("location:login.php");
    exit;
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
                
                    <li style="list-style: none;">
                    <form id="filterForm" method="POST" action="">
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
                <button name="filterRides1" type="submit">Filter</button>
            </div>
            <div class="form-group">
        <br>
        <label></label>
        <button name="filterRides" type="submit">Download</button>
    </div>
            <div class="form-group">
                <br>
                        <label></label>
                        <button name="deleteRides" type="submit" onclick="return confirm('Are you sure you want to delete these images? This action cannot be undone.');">Delete</button>
                    </div>
</form>

                    </li>
                </ul>
                <table class="table" style="text-align: center;">
        <thead class="thead-dark">
        <tr>
                <th scope="col">username</th>
                <th scope="col">vehicle</th>
                <th scope="col">customer_name</th>
                <th scope="col">Mode</th>
                <th scope="col">Type</th>
                <th scope="col">total</th>
                <th scope="col">Pickup Date</th>
                <th scope="col">Kilo Meters</th>
                <th scope="col">Ride Time</th>
                <th scope="col">Ride Picture</th>
                
                    
               
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result3)): ?>
                <?php $pic =  $row['ride_picture'] ?>
                <tr>
                    <td scope="row"><?= $row['username'] ?></td>
                    <td scope="row"><?= $row['vehicle'] ?></td>
                    <td scope="row"><?= $row['customer_name'] ?></td>
                    <td scope="row"><?= $row['mode'] ?></td>
                    <td scope="row"><?= $row['newType'] ?></td>
                    <td scope="row"><?= $row['total'] ?></td>
                    <td scope="row"><?= $row['pickup_date'] ?></td>                    
                    <td scope="row"><?= 0 +  $row['rideDistance'] ?></td>
                    <td scope="row"><?= $row['driverTime'] ?></td>
                    
                    <?php if ($row['mode'] == "Network" || $row['mode'] == "Voucher") { ?>
                        <td scope="row">
                            <a href="<?= $pic ?>" target="_blank">
                                <img src="<?= $pic ?>" alt="N/A" width="50" height="50">
                            </a>
                        </td>
                    <?php } else { ?>
                        <td scope="row">N/A</td>
                    <?php } ?>
                </tr>
            <?php endwhile; ?>
            <?php
            echo '
             <tr>
                 <td><strong>Total</strong></td>
                 <td><strong>'.$total['total'].'</strong></td>
             </tr>';
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
    
</body>


</html>