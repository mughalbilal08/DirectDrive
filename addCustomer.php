<?php
include('connect.php');
session_start();
if ($_SESSION['loggedin'] == true) {

    // Fetch the number of drivers and customers
    $sql0 = "SELECT * FROM driver_details WHERE role ='Driver'";
    $result0 = mysqli_query($conn, $sql0);
    $numberofDrivers = mysqli_num_rows($result0);

    $sql1 = "SELECT * FROM user_accounts WHERE role ='customer'";
    $result1 = mysqli_query($conn, $sql1);
    $numberofCustomers = mysqli_num_rows($result1);

    $sql2 = "SELECT SUM(total) AS total FROM expenses_details";
    $result2 = mysqli_query($conn, $sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];

    // Handle logout
    if (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        $_SESSION['email'] = "";
        $_SESSION['name'] = "";
        session_unset();
        session_destroy();
        header("location:index.html");
    } elseif (isset($_POST['add-driver'])) {
        $Name = $_POST['name'];
        $Email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
       

       

        // Insert driver details into database
        $sql = "INSERT INTO user_accounts (name, email, username, password, phone, role) VALUES('$Name', '$Email', '$username', '$password', '$phone','customer')";

        $res = mysqli_query($conn, $sql);
        if ($res == true) {
            echo '<script type="text/javascript">alert("Submitted Successfully");
            </script>';

            // window.location.href = "add-driver.php";

        } else {
            echo '<script type="text/javascript">alert("Not Submitted Please try again");</script>';
        }
    }
} else {
    header("location:login.php");
}
?>




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
            include 'adminAddStyles.php';
?>



</head>

<body>
<div class="grid-container">
        
<?php
            include 'adminSideBar.php';
?>



            <div id="container">
                <div class="container">
                    <h2>Add Customer</h2>
                    <form autocorrect="off" autocomplete="off" id="driverForm" action="addCustomer.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Name<span style="color: #f90d0d;">*</span></label>
                            <input style="color: black;" autocorrect="off" aria-autocomplete="off" autocomplete="off"
                                type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email (Optional)</label>
                            <input autocomplete="off" type="email" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="username">Username (Auto Generated)<span
                                    style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="text" id="username" name="username"
                                onclick="generateUsername()" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <!-- Add more fields as needed -->
                        <button type="submit" name="add-driver" class="button">Submit</button>
                    </form>
                </div>
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
        function generateUsername() {
            var name = document.getElementById("name").value;
            var nameWithoutSpaces = name.replace(/\s/g, '');
            var randomString = generateRandomString(5);
            var username = nameWithoutSpaces + randomString;
            document.getElementById("username").value = username;
        }

        function generateRandomString(length) {
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var result = '';

            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result;
        }


    </script>
</body>

</html>