<?php
include('connect.php');

if (isset($_POST['login'])) {
    $uname = $_POST['Username'];
    $pass = $_POST['password'];
    $error = '';

    // Check in user_accounts table
    $stmt = $conn->prepare("SELECT * FROM user_accounts WHERE username = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $username = $row['username'];
        $role = $row['role'];
        $id = $row['id'];

        // Verify password (hashed or plain text)
        if (password_verify($pass, $stored_password) || $pass === $stored_password) {
            session_start();
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $role;
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $username;

            if ($role == 'customer') {
                header("Location: customer_dashboard.php");
                exit();
            } elseif ($role == 'Admin') {
                header("Location: dashboard.php");
                exit();
            } elseif ($role == 'driver') {
                header("Location: driverdashboard.php");
                exit();
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        // Check in driver_details table
        $stmt = $conn->prepare("SELECT * FROM driver_details WHERE username = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];
            $username = $row['username'];
            $id = $row['id'];

            // Verify password (hashed or plain text)
            if (password_verify($pass, $stored_password) || $pass === $stored_password) {
                session_start();
                $_SESSION['id'] = $id;
                $_SESSION['role'] = 'Driver';
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $username;

                header("Location: driverdashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            // Check in staff_details table
            $stmt = $conn->prepare("SELECT * FROM staff_details WHERE username = ?");
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stored_password = $row['password'];
                $username = $row['username'];
                $id = $row['id'];

                // Verify password (hashed or plain text)
                if (password_verify($pass, $stored_password) || $pass === $stored_password) {
                    session_start();
                    $_SESSION['id'] = $id;
                    $_SESSION['role'] = 'Staff';
                    $_SESSION['loggedin'] = true;
                    $_SESSION['name'] = $username;

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Direct Drive</title>
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

        .error-message {
            color: #f90d0d;
            font-size: 14px;
            margin-bottom: 20px;
            opacity: 0;
            animation: fadeInDown 1s ease forwards;
            animation-delay: 0.6s;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.6s; }
        .form-group:nth-child(2) { animation-delay: 0.7s; }

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
            color: #f90d0d;
        }

        input[type="text"],
        input[type="password"] {
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
        input[type="password"]:focus {
            outline: none;
            border-color: #7F9CF5;
            box-shadow: 0 0 10px rgba(127, 156, 245, 0.3);
        }

        input[type="text"]:focus + label,
        input[type="password"]:focus + label,
        input[type="text"]:not(:placeholder-shown) + label,
        input[type="password"]:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            color: #7F9CF5;
            background: #2D3748;
            padding: 0 5px;
            border-radius: 3px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 0.8s;
        }

        .login-button,
        .signup-button {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button {
            background: linear-gradient(145deg, #805AD5, #7F9CF5);
            color: #fff;
        }

        .signup-button {
            background: transparent;
            border: 1px solid #7F9CF5;
            color: #7F9CF5;
        }

        .login-button:hover {
            background: linear-gradient(145deg, #7F9CF5, #805AD5);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(127, 156, 245, 0.4);
        }

        .signup-button:hover {
            background: #7F9CF5;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(127, 156, 245, 0.4);
        }

        .social-login {
            margin-top: 20px;
            opacity: 0;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 1.0s;
        }

        .social-login p {
            margin-bottom: 10px;
            animation: none;
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
            input[type="password"] {
                padding: 10px 12px;
                font-size: 13px;
            }

            .login-button,
            .signup-button {
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
                    <h1><span class="logo-icon"></span>Direct Drive</h1>
                </a>
            </div>
            <div class="form-container">
                <h2>Welcome to Direct Drive! 👋</h2>
                <p>Please sign-in to your account and start the adventure</p>
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form id="loginForm" action="login.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="Username" name="Username" placeholder=" " required>
                        <label for="Username">Username<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder=" " required>
                        <label for="password">Password<span style="color: #f90d0d;">*</span></label>
                    </div>
                    <div class="button-group">
                        <button type="submit" name="login" class="login-button">Login</button>
                        <button type="button" class="signup-button" onclick="window.location.href='signup.php'">Sign Up</button>
                    </div>
                </form>
                <div class="social-login">
                    <p>or</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>