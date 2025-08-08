<?php
include('connect.php');
if(isset($_POST['login'])){

    $uname = $_POST['Username'];
    $pass = $_POST['password'];

    // Check in user_accounts table
    $stmt = $conn->prepare("SELECT * FROM user_accounts WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $uname, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        // User found in user_accounts table
        session_start();
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $role = $row['role'];
        $id = $row['id'];
        $_SESSION['id'] = $id;
        $_SESSION['role'] = $role;
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $username;

        if($role == 'customer'){
            header("Location: customer_dashboard.php");
        } elseif($role == 'Admin'){
            header("Location: dashboard.php");
        }
    } else {
        // Check in driver_details table
        $stmt = $conn->prepare("SELECT * FROM driver_details WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $uname, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            // Driver found in driver_details table
            session_start();
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $id = $row['id'];
            $_SESSION['id'] = $id;
            $_SESSION['role'] = 'Driver';
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $username;

            header("Location: driverdashboard.php");
        } else{

            // Check in driver_details table
        $stmt = $conn->prepare("SELECT * FROM staff_details WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $uname, $pass);
        $stmt->execute();
        $result = $stmt->get_result();


            if($result->num_rows > 0) {
                // Driver found in driver_details table
                session_start();
                $row = $result->fetch_assoc();
                $username = $row['username'];
                $id = $row['id'];
                $_SESSION['id'] = $id;
                $_SESSION['role'] = 'Staff';
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $username;
    
                header("Location: dashboard.php");
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
    <title>Login</title>
   <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #1A202C;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    display: flex;
    max-width: 1200px;
    width: 100%;
    background-color: #2D3748;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    flex-direction: row;
}

.left-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.left-section img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.right-section {
    flex: 1;
    padding: 40px;
    background-color: #2D3748;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.logo h1 {
    font-size: 24px;
    color: #7F9CF5;
}

.logo-icon {
    font-size: 24px;
    margin-right: 10px;
}

.form-container {
    max-width: 400px;
    width: 100%;
    text-align: center;
}

h2 {
    margin-bottom: 10px;
    font-size: 24px;
}

p {
    margin-bottom: 20px;
    color: #CBD5E0;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #A0AEC0;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 14px;
}

.register-button {
    width: 100%;
    padding: 10px;
    background-color: #805AD5;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}
.loginbutton{
    width: 50%;
    padding: 10px;
    background-color: #805AD5;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.register-button:hover {
    background-color: #6B46C1;
}

.social-login {
    margin-top: 20px;
}

.social-login p {
    margin-bottom: 10px;
}

.social-icons {
    display: flex;
    justify-content: center;
}

.social-icon {
    margin: 0 5px;
}

.social-icon img {
    width: 32px;
    height: 32px;
}

/* Media Queries for Responsive Design */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }

    .left-section, .right-section {
        width: 100%;
        flex: none;
    }

    .left-section img {
        height: 200px;
    }

    .right-section {
        padding: 20px;
    }

    .form-container {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .form-group {
        margin-bottom: 10px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 8px;
        font-size: 12px;
    }

    .register-button {
        padding: 8px;
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
                <a href="index.html" style="text-decoration: none;">   <h1><span class="logo-icon">💜</span>Concord Transport</h1></a>
             
            </div>
            <div class="form-container">
                <h2>Welcome to Online Ride Booking! 👋</h2>
                <p>Please sign-in to your account and start the adventure</p>
                <form id="signupForm" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="Username">Username</label>
                        <input type="text" id="Username" name="Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="register-button">Login</button>
                </form>
               
            </div>
        </div>
    </div>
</body>
</html>
