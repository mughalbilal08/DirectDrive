<?php
include('connect.php');

if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $uname = $_POST['Username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone'];
    $role = $_POST['userType'];

    // Check if username already exists
    $check_user = $conn->prepare("SELECT COUNT(*) FROM user_accounts WHERE username = ?");
    $check_user->bind_param("s", $uname);
    $check_user->execute();
    $user_count = $check_user->get_result()->fetch_row()[0];
    if ($user_count > 0) {
        echo '<script>alert("Username already exists. Please choose a different username."); window.location.href = "signup.php";</script>';
        exit();
    }
    $check_user->close();

    // Restrict roles to customer or driver for new signups
    if ($role != 'customer' && $role != 'driver') {
        echo '<script>alert("Invalid role selected. Please choose customer or driver."); window.location.href = "signup.php";</script>';
        exit();
    }

    $conn->begin_transaction();

    // Insert into user_accounts
    $sql_user = "INSERT INTO user_accounts (name, email, password, username, phone, role, subscription_status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $status = ($role == 'driver') ? 'pending' : 'inactive'; // Changed 'active' to 'inactive' for customers
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("sssssss", $name, $email, $pass, $uname, $phone, $role, $status);
    $user_success = $stmt_user->execute();
    $user_id = $conn->insert_id;

    // If driver, insert into driver_details with additional fields
    if ($role == 'driver' && $user_success) {
        $address = $_POST['address'] ?? '';
        $passportIDate = $_POST['passportIssueDate'] ?? '';
        $passportEDate = $_POST['passportExpiryDate'] ?? '';
        $IdIDate = $_POST['IDIDate'] ?? '';
        $IdEDate = $_POST['IDEDate'] ?? '';
        $DLIDate = $_POST['DLIDate'] ?? '';
        $DLEDate = $_POST['DLEDate'] ?? '';

        // Handle file uploads
        $imageDirectory = 'datadriver/';
        function moveImageToDirectory($image, $directory) {
            global $conn;
            $imageName = $image['name'];
            if (!$imageName) return '';
            $imageTmp = $image['tmp_name'];
            $imagePath = $directory . $imageName;
            move_uploaded_file($imageTmp, $imagePath);
            return mysqli_real_escape_string($conn, $imagePath);
        }

        $profileImageData = moveImageToDirectory($_FILES['profileImage'] ?? [], $imageDirectory);
        $passportImageData = moveImageToDirectory($_FILES['passportImage'] ?? [], $imageDirectory);
        $idCardImageData = moveImageToDirectory($_FILES['idCardImage'] ?? [], $imageDirectory);
        $drivingLicenseImageData = moveImageToDirectory($_FILES['drivingLicenseImage'] ?? [], $imageDirectory);

        $sql_driver = "INSERT INTO driver_details 
            (id, Name, Email, Address, username, password, phone, profile_img, passportnum, 
             passportIDate, passportEDate, IdNum, IdIDate, IdEDate, DLicenseNum, 
             DLIDate, DLEDate, role, subscription_status) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Driver', 'pending')";
        $stmt_driver = $conn->prepare($sql_driver);
        $stmt_driver->bind_param("issssssssssssssss", 
            $user_id, $name, $email, $address, $uname, $pass, $phone, $profileImageData, 
            $passportImageData, $passportIDate, $passportEDate, $idCardImageData, 
            $IdIDate, $IdEDate, $drivingLicenseImageData, $DLIDate, $DLEDate);
        $driver_success = $stmt_driver->execute();
        $stmt_driver->close();
    } else {
        $driver_success = true; // No driver_details for customers
    }

    if ($user_success && $driver_success) {
        $conn->commit();
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $uname;
        $_SESSION['role'] = $role;
        $_SESSION['id'] = $user_id;

        // Redirect to login for both roles
        echo '<script>alert("Registration successful! Please log in."); window.location.href = "login.php";</script>';
        exit();
    } else {
        $conn->rollback();
        echo '<script>alert("Error creating account. Please try again."); window.location.href = "signup.php";</script>';
    }
    $stmt_user->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Direct Drive</title>
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Background overlay for subtle texture */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/dark-mosaic.png');
            opacity: 0.05;
            z-index: 0;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 90%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .left-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(145deg, #2D3748, #1A202C);
            padding: 20px;
        }

        .left-section img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.5s ease;
        }

        .left-section img:hover {
            transform: scale(1.05);
        }

        .right-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: transparent;
            overflow-y: auto; /* Allow scrolling for long forms */
            max-height: 80vh; /* Limit height to enable scrolling */
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInDown 1s ease forwards;
        }

        .logo h1 {
            font-size: 28px;
            font-weight: 600;
            color: #7F9CF5;
            letter-spacing: 1px;
        }

        .logo-icon {
            font-size: 28px;
            margin-right: 10px;
            color: #7F9CF5;
        }

        .form-container {
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #fff;
            opacity: 0;
            animation: fadeInDown 1s ease forwards;
            animation-delay: 0.2s;
        }

        p {
            margin-bottom: 30px;
            color: #CBD5E0;
            font-size: 16px;
            font-weight: 300;
            opacity: 0;
            animation: fadeInDown 1s ease forwards;
            animation-delay: 0.4s;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
        }

        /* Staggered animation delays for form groups */
        .form-group:nth-child(1) { animation-delay: 0.6s; }
        .form-group:nth-child(2) { animation-delay: 0.7s; }
        .form-group:nth-child(3) { animation-delay: 0.8s; }
        .form-group:nth-child(4) { animation-delay: 0.9s; }
        .form-group:nth-child(5) { animation-delay: 1.0s; }
        .form-group:nth-child(6) { animation-delay: 1.1s; }
        #driverDetails .form-group:nth-child(1) { animation-delay: 1.2s; }
        #driverDetails .form-group:nth-child(2) { animation-delay: 1.3s; }
        #driverDetails .form-group:nth-child(3) { animation-delay: 1.4s; }
        #driverDetails .form-group:nth-child(4) { animation-delay: 1.5s; }
        #driverDetails .form-group:nth-child(5) { animation-delay: 1.6s; }
        #driverDetails .form-group:nth-child(6) { animation-delay: 1.7s; }
        #driverDetails .form-group:nth-child(7) { animation-delay: 1.8s; }
        #driverDetails .form-group:nth-child(8) { animation-delay: 1.9s; }
        #driverDetails .form-group:nth-child(9) { animation-delay: 2.0s; }
        #driverDetails .form-group:nth-child(10) { animation-delay: 2.1s; }
        #driverDetails .form-group:nth-child(11) { animation-delay: 2.2s; }

        label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 14px;
            color: #A0AEC0;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        label span {
            color: #f90d0d; /* Retain the red asterisk for required fields */
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="file"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #7F9CF5;
            box-shadow: 0 0 10px rgba(127, 156, 245, 0.3);
        }

        input[type="text"]:focus + label,
        input[type="email"]:focus + label,
        input[type="password"]:focus + label,
        input[type="file"]:focus + label,
        input[type="date"]:focus + label,
        select:focus + label,
        input[type="text"]:not(:placeholder-shown) + label,
        input[type="email"]:not(:placeholder-shown) + label,
        input[type="password"]:not(:placeholder-shown) + label,
        input[type="file"]:not(:placeholder-shown) + label,
        input[type="date"]:not(:placeholder-shown) + label,
        select:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            color: #7F9CF5;
            background: #2D3748;
            padding: 0 5px;
            border-radius: 3px;
        }

        /* Style for file inputs to match the design */
        input[type="file"] {
            padding: 10px 15px; /* Adjust padding for file inputs */
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: rgba(255, 255, 255, 0.05) url('data:image/svg+xml;utf8,<svg fill="white" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
            background-size: 16px;
        }

        #driverDetails {
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        #driverDetails.active {
            display: block;
            opacity: 1;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 2.3s;
        }

        .register-button,
        .login-button {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .register-button {
            background: linear-gradient(145deg, #805AD5, #7F9CF5);
            color: #fff;
        }

        .login-button {
            background: transparent;
            border: 1px solid #7F9CF5;
            color: #7F9CF5;
        }

        .register-button:hover {
            background: linear-gradient(145deg, #7F9CF5, #805AD5);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(127, 156, 245, 0.4);
        }

        .login-button:hover {
            background: #7F9CF5;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(127, 156, 245, 0.4);
        }

        .social-login {
            margin-top: 20px;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 2.5s;
        }

        .social-login p {
            margin-bottom: 10px;
            animation: none; /* Remove animation from this nested p */
        }

        /* Animations */
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 95%;
            }

            .left-section, .right-section {
                width: 100%;
                flex: none;
            }

            .left-section img {
                height: 250px;
                border-radius: 15px 15px 0 0;
            }

            .right-section {
                padding: 30px;
                max-height: none; /* Remove max-height on mobile for better scrolling */
            }

            .form-container {
                width: 100%;
            }

            .logo h1 {
                font-size: 24px;
            }

            .logo-icon {
                font-size: 24px;
            }

            h2 {
                font-size: 22px;
            }

            p {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 90%;
            }

            .left-section img {
                height: 200px;
            }

            .right-section {
                padding: 20px;
            }

            .logo h1 {
                font-size: 20px;
            }

            .logo-icon {
                font-size: 20px;
            }

            h2 {
                font-size: 20px;
            }

            p {
                font-size: 13px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="file"],
            input[type="date"],
            select {
                padding: 10px 12px;
                font-size: 13px;
            }

            .register-button,
            .login-button {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <img src="images/jeep.png" alt="Urban Ride Image">
        </div>
        <div class="right-section">
            <div class="logo">
                <a href="index.html" style="text-decoration: none;">
                    <h1><span class="logo-icon">💜</span>Concord Transport</h1>
                </a>
            </div>
            <div class="form-container">
                <h2>Welcome to Direct Drive! 👋</h2>
                <p>Create your account and start the adventure</p>
                <form id="signupForm" action="signup.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder=" " required>
                        <label for="name">Name<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder=" " required>
                        <label for="email">Email Address<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="Username" name="Username" placeholder=" " required>
                        <label for="Username">Username<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder=" " required>
                        <label for="password">Password<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="phone" name="phone" placeholder=" " required>
                        <label for="phone">Phone<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <select name="userType" id="userType" required onchange="toggleDriverDetails()">
                            <option value="" disabled selected hidden></option>
                            <option value="customer">Ride Customer</option>
                            <option value="driver">Driver</option>
                        </select>
                        <label for="userType">Select Role<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <!-- Driver-specific fields -->
                    <div id="driverDetails">
                        <div class="form-group">
                            <input type="text" id="address" name="address" placeholder=" ">
                            <label for="address">Address<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="file" id="profileImage" name="profileImage" placeholder=" " accept=".jpg, .png, .jpeg">
                            <label for="profileImage">Profile Image (Optional)</label>
                        </div>
                        <div class="form-group">
                            <input type="file" id="passportImage" name="passportImage" placeholder=" " accept=".jpg, .png, .jpeg">
                            <label for="passportImage">Passport Image<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="passportIssueDate" name="passportIssueDate" placeholder=" ">
                            <label for="passportIssueDate">Passport Issue Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="passportExpiryDate" name="passportExpiryDate" placeholder=" ">
                            <label for="passportExpiryDate">Passport Expiry Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="file" id="idCardImage" name="idCardImage" placeholder=" " accept=".jpg, .png, .jpeg">
                            <label for="idCardImage">ID Card Image<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="IDIDate" name="IDIDate" placeholder=" ">
                            <label for="IDIDate">ID Card Issue Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="IDEDate" name="IDEDate" placeholder=" ">
                            <label for="IDEDate">ID Card Expiry Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="file" id="drivingLicenseImage" name="drivingLicenseImage" placeholder=" " accept=".jpg, .png, .jpeg">
                            <label for="drivingLicenseImage">Driving License Image<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="DLIDate" name="DLIDate" placeholder=" ">
                            <label for="DLIDate">Driving License Issue Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                        <div class="form-group">
                            <input type="date" id="DLEDate" name="DLEDate" placeholder=" ">
                            <label for="DLEDate">Driving License Expiry Date<span style="color: #f90d0d;">*</span></label>
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="submit" name="signup" class="register-button">Register</button>
                        <button type="button" class="login-button" onclick="window.location.href='login.php'">Login</button>
                    </div>
                </form>
                <div class="social-login">
                    <p>or</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleDriverDetails() {
            const role = document.getElementById('userType').value;
            const driverDetails = document.getElementById('driverDetails');
            if (role === 'driver') {
                driverDetails.classList.add('active');
                driverDetails.querySelectorAll('input:not([name="profileImage"])').forEach(input => {
                    input.required = true;
                });
            } else {
                driverDetails.classList.remove('active');
                driverDetails.querySelectorAll('input').forEach(input => {
                    input.required = false;
                });
            }
        }
    </script>
</body>
</html>