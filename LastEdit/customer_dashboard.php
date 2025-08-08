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

    if (isset($_FILES["air-ticket"])) {
        $target_file = $target_dir . basename($_FILES["air-ticket"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size (not more than 5MB)
        if ($_FILES["air-ticket"]["size"] > 5000000) {
            echo '<script>alert("Sorry, your file is too large.");</script>';
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "pdf") {
            echo '<script>alert("Sorry, only JPG and PDF files are allowed.");</script>';
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["air-ticket"]["tmp_name"], $target_file)) {
                // Insert ride details into database
                $sql = "INSERT INTO `booking_detail` (`customer_id`, `customer_name`, `pickup_location`, `pickup_date`, `pickup_time`, `NumOfPassengers`, `description`, `drop_location`, `ride_status`, `air_ticket`, `customer_email`, `role`) 
                        VALUES ('$customer_id', '$customer_name', '$pickup_location', '$pickup_date', '$pickup_time', '$NumOfPassengers', '$description', '$drop_location', '$ride_status', '$target_file', '$user_email', 'customer')";
                
                if (mysqli_query($conn, $sql)) {
                    
                    $last_id = mysqli_insert_id($conn);
    
                    $message = "Customer: '$customer_name' has requested a ride From '$pickup_location' to '$drop_location' at Date: '$pickup_date' and Time: '$pickup_time' with Description: '$description'";

                    // Prepare and execute the insertion into notifications with the last inserted id
                    $stmt = $conn->prepare("INSERT INTO `notifications` (`message`, `role`, `status`, `CustomerId`, `rideId`) VALUES (?, 'customer', 'Requested', ?, ?)");
                    $stmt->bind_param("ssi", $message, $customer_id, $last_id);
                    $stmt->execute();

                    $stmt->close();
                    $to = 'royalenfield3211@gmail.com';
                    $subject = 'New Ride Booking';
                    $message = "
                        <html>
                        <head>
                            <title>New Ride Booking</title>
                        </head>
                        <body>
                            <h2>Ride Booking Details</h2>
                            <p><strong>Name of Passenger:</strong> $customer_name</p>
                            <p><strong>Pickup Location:</strong> $pickup_location</p>
                            <p><strong>Pickup Date:</strong> $pickup_date</p>
                            <p><strong>Pickup Time:</strong> $pickup_time</p>
                            <p><strong>Number of Passengers:</strong> $NumOfPassengers</p>
                            <p><strong>Description:</strong> $description</p>
                            <p><strong>Drop Location:</strong> $drop_location</p>
                            <p>Please find the air ticket attached.</p>
                        </body>
                        </html>
                    ";

                    // Boundary for multipart email
                    $boundary = md5(time());

                    // Headers for HTML email and attachment
                    $headers = "From: your_email@example.com\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

                    // Message Body
                    $body = "--$boundary\r\n";
                    $body .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $body .= $message . "\r\n\r\n";

                    // Attachment
                    $file = fopen($target_file, 'r');
                    $content = fread($file, filesize($target_file));
                    fclose($file);
                    $encoded_content = chunk_split(base64_encode($content));

                    $body .= "--$boundary\r\n";
                    $body .= "Content-Type: application/octet-stream; name=\"" . basename($target_file) . "\"\r\n";
                    $body .= "Content-Description: " . basename($target_file) . "\r\n";
                    $body .= "Content-Disposition: attachment; filename=\"" . basename($target_file) . "\";\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $body .= $encoded_content . "\r\n";
                    $body .= "--$boundary--";

                    // Send email
                    if (mail($to, $subject, $body, $headers)) {
                        echo '<script>alert("Booking successfully. Booking details sent to admin."); window.location.href = "customer_dashboard.php";</script>';
                    } else {
                        error_log("Mail sending failed: " . error_get_last()['message']);
                        echo '<script>alert("Error sending booking details to admin."); window.location.href = "customer_dashboard.php";</script>';
                    }
                } else {
                    echo '<script>alert("Error: ' . mysqli_error($conn) . '");</script>';
                }
            } else {
                echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
            }
        }
    } else {
        echo '<script>alert("No file uploaded.");</script>';
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
            <div class="container">
                <h1>Book Your Ride Now!</h1>
                <form method="POST" action="customer_dashboard.php" enctype="multipart/form-data">
    <div class="form-group">
        <label for="pickup-date">Pick Up Date <span style="color:red;">*</span></label>
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
        <label for="pname">Name Of Passenger </label>
        <input type="text" id="pname" name="pname" placeholder="Passenger name">
    </div>
    <div class="form-group">
        <label for="passengers">No of Passengers <span style="color:red;">*</span></label>
        <input type="number" id="passengers" name="passengers" placeholder="No of Passengers" required>
    </div>
    <div class="form-group">
        <label for="drop-location">Drop Location</label>
        <input type="text" id="drop-location" name="drop-location" placeholder="Drop Location">
    </div>
    <div class="form-group full-width">
        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Description"></textarea>
    </div>
    <div class="form-group full-width">
        <label for="air-ticket">Upload Your Air Ticket Information (PDF or JPG only) <span style="color:red;">*</span></label>
        <input type="file" id="air-ticket" name="air-ticket" accept=".pdf,.jpg" onchange="validateFile(this)" required>
        <span id="file-error" class="error-message"></span>
    </div>
    <button name="add-ride" type="submit">Book</button>
</form>

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

