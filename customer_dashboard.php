<?php
include('connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Set user type to customer
$_SESSION['user_type'] = 'customer';

$custid = $_SESSION['id'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Handle form submission for ride booking
if (isset($_POST['add-ride'])) {
    $customer_id = $_SESSION['id'];
    $customer_name = $_POST['pname'];
    $pickup_location = $_POST['plocation'];
    $pickup_date = $_POST['pickup-date'];
    $pickup_time = $_POST['pickup-time'];
    $NumOfPassengers = $_POST['passengers'];
    $description = $_POST['description'];
    $drop_location = $_POST['dlocation'];
    $ride_status = 'Booked';
    $user_email = $email;

    // File upload handling
    $target_dir = "Uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES["air-ticket"])) {
        $target_file = $target_dir . basename($_FILES["air-ticket"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["air-ticket"]["size"] > 5000000) {
            echo '<script>alert("Sorry, your file is too large.");</script>';
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "pdf") {
            echo '<script>alert("Sorry, only JPG and PDF files are allowed.");</script>';
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["air-ticket"]["tmp_name"], $target_file)) {
                $sql = "INSERT INTO `booking_detail` (`customer_id`, `customer_name`, `pickup_location`, `pickup_date`, `pickup_time`, `NumOfPassengers`, `description`, `drop_location`, `ride_status`, `air_ticket`, `customer_email`, `role`) 
                        VALUES ('$customer_id', '$customer_name', '$pickup_location', '$pickup_date', '$pickup_time', '$NumOfPassengers', '$description', '$drop_location', '$ride_status', '$target_file', '$user_email', 'customer')";
                
                if (mysqli_query($conn, $sql)) {
                    $last_id = mysqli_insert_id($conn);
                    $message = "Customer: '$customer_name' has requested a ride From '$pickup_location' to '$drop_location' at Date: '$pickup_date' and Time: '$pickup_time' with Description: '$description'";
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

                    $boundary = md5(time());
                    $headers = "From: your_email@example.com\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
                    $body = "--$boundary\r\n";
                    $body .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $body .= $message . "\r\n\r\n";
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

// Check payment credentials and subscription status
$hasPaymentQuery = mysqli_query($conn, "SELECT * FROM payment_credentials WHERE user_id = '$custid'");
$hasPayment = mysqli_num_rows($hasPaymentQuery) > 0;

// Debug: Log the result of the payment credentials check
error_log("Has payment credentials for user $custid: " . ($hasPayment ? 'Yes' : 'No'));

// Use the session's selected plan to determine if the upload button should appear
$selectedPlan = $_SESSION['selected_plan'] ?? 'basic';

// Debug: Log the selected plan
error_log("Selected plan in customer_dashboard.php: $selectedPlan");

// Fallback to database if session is not set (for after approval)
if (!$selectedPlan || $selectedPlan === 'basic') {
    $dbPlan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT subscription_type FROM user_accounts WHERE id = '$custid' AND role = 'customer'"))['subscription_type'] ?? 'basic';
    if ($dbPlan !== 'basic') {
        $selectedPlan = $dbPlan;
    }
    error_log("Fallback to database plan: $dbPlan");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Direct Drive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="css/dashboardstyle.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-color: #1A202C; color: #fff; min-height: 100vh; overflow-x: hidden; }
        .grid-container { display: flex; width: 100%; position: relative; }
        .header { background-color: #2D3748; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); }
        .menu-icon { cursor: pointer; color: #7F9CF5; }
        .header-right label { font-size: 1.1rem; color: #CBD5E0; }
        #sidebar { width: 250px; min-width: 250px; background-color: #2D3748; padding-top: 70px; position: fixed; top: 0; left: 0; height: 100%; z-index: 999; transition: transform 0.3s ease; }
        #sidebar.closed { transform: translateX(-190px); width: 250px; }
        .main-container { width: 70%; padding: 20px 40px 40px; background-color: #1A202C; margin-left: 310px; }
        .main-title h2 { font-size: 2rem; font-weight: 600; color: #7F9CF5; text-align: center; margin-bottom: 20px; animation: slideIn 0.5s ease; }
        .welcome-message { text-align: center; font-size: 1.2rem; color: #CBD5E0; margin-bottom: 30px; animation: fadeIn 0.8s ease; }
        .dashboard-cards { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; margin-bottom: 40px; }
        .dashboard-card { flex: 1; min-width: 200px; background-color: #2D3748; border-radius: 10px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; animation: fadeIn 1s ease; }
        .dashboard-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); }
        .dashboard-card .icon { font-size: 2.5rem; color: #7F9CF5; margin-bottom: 10px; }
        .dashboard-card h3 { font-size: 1.1rem; color: #CBD5E0; margin-bottom: 10px; }
        .dashboard-card .value { font-size: 1.5rem; font-weight: 600; color: #fff; }
        .ride-form-container { background-color: #2D3748; border-radius: 10px; padding: 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); width: 100%; max-width: 600px; margin: 0 auto; animation: fadeIn 0.8s ease; }
        .ride-form-container h1 { font-size: 1.6rem; font-weight: 700; color: #7F9CF5; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #CBD5E0; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #4A5568; border-radius: 5px; background-color: #1A202C; color: #fff; font-size: 1rem; }
        .form-group textarea { resize: vertical; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #7F9CF5; box-shadow: 0 0 5px rgba(127, 156, 245, 0.3); }
        .form-group-container { display: flex; gap: 10px; }
        .form-group-container .form-group { flex: 1; }
        .error-message { color: #EF4444; font-size: 0.9rem; margin-top: 5px; }
        #map { height: 400px; width: 100%; margin-top: 20px; border-radius: 5px; }
        .location-container { margin-top: 10px; }
        .location-container strong { color: #CBD5E0; }
        .location-container input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #4A5568; border-radius: 5px; background-color: #1A202C; color: #fff; }
        button, .form-group-container button { width: 100%; padding: 12px; background-color: #805AD5; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background-color 0.3s ease, transform 0.2s ease; }
        button:hover, .form-group-container button:hover { background-color: #6B46C1; transform: scale(1.05); }
        .upload-button { background-color: #28a745; margin-top: 20px; }
        .upload-button:hover { background-color: #218838; }
        .sidebar-section { padding: 12px 20px; margin: 8px 0; font-size: 1.1rem; cursor: pointer; color: #fff; background: #805AD5; border-radius: 8px; transition: all 0.3s ease; }
        .sidebar-section:hover { background: #6B46C1; }
        .sidebar-sublist { display: none; padding-left: 20px; }
        .sidebar-list-item a { color: #CBD5E0; text-decoration: none; transition: color 0.3s ease; }
        .sidebar-list-item a:hover { color: #7F9CF5; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @media (max-width: 1024px) { .main-container { padding: 20px 30px 30px; } .dashboard-card { min-width: 150px; } }
        @media (max-width: 768px) { .grid-container { flex-direction: column; } #sidebar { width: 100%; min-width: 100%; padding-top: 60px; position: relative; transform: none; } #sidebar.closed { transform: none; display: none; } .main-container { padding: 20px 20px 20px; margin-left: 0; width: 100%; } .main-title h2 { font-size: 1.8rem; } .dashboard-cards { flex-direction: column; align-items: center; } .ride-form-container { width: 100%; } }
        @media (max-width: 480px) { .main-container { padding: 20px 15px 15px; } .main-title h2 { font-size: 1.5rem; } .ride-form-container h1 { font-size: 1.4rem; } .form-group label { font-size: 0.9rem; } .form-group input, .form-group textarea { font-size: 0.9rem; } button { padding: 10px; font-size: 1rem; } }
    </style>
</head>
<body>
    <div class="grid-container">
        <header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <label style="margin-left: 35px;"><?php echo $_SESSION['name'];?></label>
            </div>
        </header>
        <?php include 'customerSideBar.php'; ?>
        <main class="main-container">
            <div class="main-title">
                <h2>Customer Dashboard</h2>
            </div>
            <div class="welcome-message">
                Welcome, <?php echo $_SESSION['name']; ?>! Here's your overview.
            </div>
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="icon"><i class='bx bxs-check-circle'></i></div>
                    <h3>Completed Rides</h3>
                    <div class="value"><?php echo $numberofCompletedRides; ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class='bx bxs-time'></i></div>
                    <h3>Pending Rides</h3>
                    <div class="value"><?php echo $numberofPendingRides; ?></div>
                </div>
            </div>
            <div class="ride-form-container">
                <h1>Book Your Ride Now!</h1>
                <form method="POST" action="customer_dashboard.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="pickup-date">Pick Up Date <span style="color:#EF4444;">*</span></label>
                        <input type="date" id="pickup-date" name="pickup-date" required>
                    </div>
                    <div class="form-group">
                        <label for="pickup-time">Pick Up Time</label>
                        <input type="time" id="pickup-time" name="pickup-time">
                    </div>
                    <div class="form-group">
                        <label for="pname">Name Of Passenger</label>
                        <input type="text" id="pname" name="pname" placeholder="Passenger name">
                    </div>
                    <div class="form-group">
                        <label for="passengers">No of Passengers <span style="color:#EF4444;">*</span></label>
                        <input type="number" id="passengers" name="passengers" placeholder="No of Passengers" required>
                    </div>
                    <div class="form-group-container">
                        <div class="form-group">
                            <button id="from-location-btn" type="button" onclick="setLocation('from')">Set Pickup Location</button>
                            <input type="hidden" id="from-location" name="pickup-location">
                        </div>
                        <div class="form-group">
                            <button id="to-location-btn" type="button" onclick="setLocation('to')">Set Destination</button>
                            <input type="hidden" id="to-location" name="drop-location">
                        </div>
                    </div>
                    <div id="map"></div>
                    <div class="location-container">
                        <strong>Pickup Location: </strong>
                        <input type="text" name="plocation" id="pickup_location" value="None" readonly>
                        <strong>Destination: </strong>
                        <input type="text" name="dlocation" id="drop_location" value="None" readonly>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="air-ticket">Upload Your Air Ticket Information (PDF or JPG only) <span style="color:#EF4444;">*</span></label>
                        <input type="file" id="air-ticket" name="air-ticket" accept=".pdf,.jpg" onchange="validateFile(this)" required>
                        <span id="file-error" class="error-message"></span>
                    </div>
                    <button name="add-ride" type="submit">Book</button>
                </form>
                <?php if ($hasPayment && $selectedPlan !== 'basic'): ?>
                    <form method="POST" action="upload_receiptC.php" style="text-align: center;">
                        <button type="submit" class="upload-button">Upload Receipt for Admin Approval</button>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="dashboardscript.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const isClosed = localStorage.getItem('sidebarClosed') === 'true';
            if (isClosed) sidebar.classList.add('closed');
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) sidebar.classList.add('closed');
            });
        });
        function openSidebar() { document.getElementById('sidebar').classList.remove('closed'); localStorage.setItem('sidebarClosed', 'false'); }
        function closeSidebar() { document.getElementById('sidebar').classList.add('closed'); localStorage.setItem('sidebarClosed', 'true'); }
        let map, fromMarker, toMarker;
        let selectedLocationType = '';
        window.onload = function () {
            map = L.map('map').setView([37.7749, -122.4194], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            map.on('click', function (e) {
                if (selectedLocationType === 'from') {
                    if (fromMarker) map.removeLayer(fromMarker);
                    fromMarker = L.marker(e.latlng).addTo(map);
                    document.getElementById('from-location').value = e.latlng.lat + ',' + e.latlng.lng;
                    getLocationName(e.latlng, 'pickup_location');
                } else if (selectedLocationType === 'to') {
                    if (toMarker) map.removeLayer(toMarker);
                    toMarker = L.marker(e.latlng).addTo(map);
                    document.getElementById('to-location').value = e.latlng.lat + ',' + e.latlng.lng;
                    getLocationName(e.latlng, 'drop_location');
                }
                selectedLocationType = '';
            });
        };
        function setLocation(type) {
            selectedLocationType = type;
            alert("Click on the map to set your " + (type === 'from' ? "Pickup" : "Destination") + " location.");
        }
        function getLocationName(latlng, outputId) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`)
                .then(response => response.json())
                .then(data => { if (data && data.display_name) document.getElementById(outputId).value = data.display_name; })
                .catch(err => console.error(err));
        }
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