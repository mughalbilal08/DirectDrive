<?php
include('connect.php');
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location:login.php");
    exit;
}

$custid = $_SESSION['id'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$sql3 ="SELECT * from notifications WHERE CustomerId = '$custid'";
$result3 = mysqli_query($conn,$sql3);

// Handle logout
if (isset($_GET['logout'])) {
    $_SESSION['loggedin'] = false;
    $_SESSION['email'] = "";
    $_SESSION['name'] = "";
    session_unset();
    session_destroy();
    header("location:index.html");
    exit;
}

// Handle form submission
if (isset($_POST['add-ride'])) {
    $customer_id = $_SESSION['id'];
    $customer_name = $_POST['pname'];
    $pickup_location = $_POST['pickup-location'];
    $pickup_date = $_POST['pickup-date'];
    $pickup_time = $_POST['pickup-time'];
    $NumOfPassengers = $_POST['passengers'];
    $description = $_POST['description'];
    $drop_location = $_POST['drop-location'];
    $ride_status = 'Booked';
    $user_email = $email; // Retrieve user email from session

    // File upload handling
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    
}

// Retrieve completed rides count
$sql0 = "SELECT * FROM booking_detail WHERE customer_id = '$custid' AND ride_status = 'Completed'";
$result0 = mysqli_query($conn, $sql0);
$numberofCompletedRides = mysqli_num_rows($result0);

// Retrieve pending rides count
$sql1 = "SELECT * FROM booking_detail WHERE customer_id = '$custid' AND ride_status != 'Completed'";
$result1 = mysqli_query($conn, $sql1);
$numberofPendingRides = mysqli_num_rows($result1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Customer Dashboard</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
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
            background-color: #2c1455;
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
                flex                : 1 1 100%;
            }

            button {
                flex: 1 1 100%;
            }
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
<?php
            include 'cardStyles.php';
?>

</head>

<body>
    <div class="grid-container">

        <?php
            include 'customerSideBar.php';
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
                            <th scope="col">Request</th>
                            <th scope="col">Status</th>
                            <th scope="col">Time Stamp</th>
                            <th scope="col">Driver</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          
                       while($row =mysqli_fetch_assoc($result3)){
                        // Fetch profile picture path from database
                           
                            
                        ?>
                        <tr>

                            
                            <td scope="row"><?php echo $row['message']; ?></td>
                            <td scope="row"><?php echo $row['status']; ?></td>
                            <td scope="row"><?php echo $row['createdAt']; ?></td>
                            
                           <?php
                           $driverId = $row['DriverId'];

                           $sql9 = "SELECT IFNULL(username, 'No Driver') AS Driver FROM driver_details WHERE id = '$driverId'";
                           $result9 = mysqli_query($conn, $sql9);
                           
                                                    
                            if ($result9 && mysqli_num_rows($result9) > 0) {
                               $dRow = mysqli_fetch_assoc($result9);
                               $driverName = $dRow['Driver'];
                           } else {
                               // In case the query fails, you can set a default value.
                               $driverName = 'No Driver';
                           }
                            ?> 
                           <td scope="row"><?php echo $driverName; ?></td>
                           
                            
                         
                            
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
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="dashboardscript.js"></script>

    <script>
        function validateFile(input) {
            var filePath = input.value;
            var allowedExtensions = /(\.pdf|\.jpg)$/i;
            var errorMessage = document.getElementById('file-error');

            if (!allowedExtensions.exec(filePath)) {
                errorMessage.textContent = 'Invalid file type. Please upload only PDF or JPG files.';
                input.value = '';
                return false;
            } else {
                errorMessage.textContent = '';
                return true;
            }
        }
    </script>
</body>

</html>

