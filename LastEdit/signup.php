<?php
include('connect.php');
if(isset($_POST['signup'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $uname = $_POST['Username'];
    $pass = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['userType'];
    $sql= "INSERT INTO user_accounts (name,email,password,username,phone,role) VALUES('$name','$email','$pass','$uname','$phone','$role')";
    $res =mysqli_query($conn,$sql);
if($res=true){
    echo '<script type="text/javascript">alert("User account created successfully");
     window.location.href = "login.php";
    </script>';
}else{
    echo '<script type="text/javascript">alert("Please fill all the required fileds ");
   </script>';
}

}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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
            padding: 5px;
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

        .loginbutton {
            text-decoration: none;
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

            .left-section,
            .right-section {
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
                padding: 4px;
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
                <a href="index.html" style="text-decoration: none;">
                    <h1><span class="logo-icon">💜</span>Concord Transport</h1>
                </a>
            </div>
            <div class="form-container">
                <h2>Welcome to Online Ride Booking! 👋</h2>
                <p>Please sign-in to your account and start the adventure</p>
                <form id="signupForm" action="signup.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name<span style="color: #f90d0d;">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address<span style="color: #f90d0d;">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Username<span style="color: #f90d0d;">*</span></label>
                        <input type="text" id="Username" name="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password<span style="color: #f90d0d;">*</span></label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone<span style="color: #f90d0d;">*</span></label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group" style="width: 100%;">
                        <label for="userType">Select Role<span style="color: #f90d0d;">*</span></label>
                        <select name="userType" id="userType" required>
                            <option value="">Select role</option>
                            <option value="customer">Ride Customer</option>
                            
                        </select>
                    </div>
                    <button type="submit" name="signup" class="register-button">Register</button>
                </form>
                <div class="social-login">
                    <p>or</p>
                    <button type="button" class="loginbutton"><a style="text-decoration: none;color: #CBD5E0;"
                            href="login.php">Login</a></button>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php

?>