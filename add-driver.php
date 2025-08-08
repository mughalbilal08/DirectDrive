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
        $Address = $_POST['address'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $passportIDate = $_POST['passportIssueDate'];
        $passportEDate = $_POST['passportExpiryDate'];
       
        $IdIDate = $_POST['IDIDate'];
        $IdEDate = $_POST['IDEDate'];
        $RtaIDate = $_POST['RtaIDate'];
        $RtaEDate = $_POST['RtaEDate'];
        $DLIDate = $_POST['DLIDate'];
        $DLEDate = $_POST['DLEDate'];
        $Beneficery_name = $_POST['beneficiaryName'];
        $Iban = $_POST['iban'];
        $bankname = $_POST['bankName'];
        $branchName = $_POST['branchName'];
        $InsuranceIDate = $_POST['InsuranceIDate'];
        $InsuranceEDate = $_POST['InsuranceEDate'];

        // Handle profile image
        $profileImage = $_FILES['profileImage'];
        $profileImageName = $profileImage['name'];
        $profileImageTmp = $profileImage['tmp_name'];
        $profileImageType = $profileImage['type'];
        $profileImageSize = $profileImage['size'];
        $profileImageError = $profileImage['error'];

        // Handle passport image
        $passportImage = $_FILES['passportImage'];
        $passportImageName = $passportImage['name'];
        $passportImageTmp = $passportImage['tmp_name'];
        $passportImageType = $passportImage['type'];
        $passportImageSize = $passportImage['size'];
        $passportImageError = $passportImage['error'];

        // Handle visa image
        $visaImage = $_FILES['visaImage'];
        $visaImageName = $visaImage['name'];
        $visaImageTmp = $visaImage['tmp_name'];
        $visaImageType = $visaImage['type'];
        $visaImageSize = $visaImage['size'];
        $visaImageError = $visaImage['error'];

        // Handle ID card image
        $idCardImage = $_FILES['idCardImage'];
        $idCardImageName = $idCardImage['name'];
        $idCardImageTmp = $idCardImage['tmp_name'];
        $idCardImageType = $idCardImage['type'];
        $idCardImageSize = $idCardImage['size'];
        $idCardImageError = $idCardImage['error'];

        // Handle RTA card image
        $rtaCardImage = $_FILES['rtaCardImage'];
        $rtaCardImageName = $rtaCardImage['name'];
        $rtaCardImageTmp = $rtaCardImage['tmp_name'];
        $rtaCardImageType = $rtaCardImage['type'];
        $rtaCardImageSize = $rtaCardImage['size'];
        $rtaCardImageError = $rtaCardImage['error'];

        // Handle driving license image
        $drivingLicenseImage = $_FILES['drivingLicenseImage'];
        $drivingLicenseImageName = $drivingLicenseImage['name'];
        $drivingLicenseImageTmp = $drivingLicenseImage['tmp_name'];
        $drivingLicenseImageType = $drivingLicenseImage['type'];
        $drivingLicenseImageSize = $drivingLicenseImage['size'];
        $drivingLicenseImageError = $drivingLicenseImage['error'];

        // Handle insurance image
        $insuranceImage = $_FILES['insuranceImage'];
        $insuranceImageName = $insuranceImage['name'];
        $insuranceImageTmp = $insuranceImage['tmp_name'];
        $insuranceImageType = $insuranceImage['type'];
        $insuranceImageSize = $insuranceImage['size'];
        $insuranceImageError = $insuranceImage['error'];

        // Check if any file upload error occurred
        $uploadErrors = [$profileImageError, $passportImageError, $visaImageError, $idCardImageError, $rtaCardImageError, $drivingLicenseImageError, $insuranceImageError];
        if (in_array(1, $uploadErrors) || in_array(2, $uploadErrors) || in_array(3, $uploadErrors)) {
            echo '<script>alert("Error uploading files. Please try again.");</script>';
            exit;
        }

        // Directory where images will be saved
        $imageDirectory = 'datadriver/';

        // Function to move uploaded files to directory and return image path
        function moveImageToDirectory($image, $directory)
        {
            global $conn;
            $imageName = $image['name'];
            $imageTmp = $image['tmp_name'];
            $imagePath = $directory . $imageName;
            move_uploaded_file($imageTmp, $imagePath);
            return mysqli_real_escape_string($conn, $imagePath);
        }

        // Move images to directory and get their paths
        $profileImageData = moveImageToDirectory($profileImage, $imageDirectory);
        $passportImageData = moveImageToDirectory($passportImage, $imageDirectory);
        $visaImageData = moveImageToDirectory($visaImage, $imageDirectory);
        $idCardImageData = moveImageToDirectory($idCardImage, $imageDirectory);
        $rtaCardImageData = moveImageToDirectory($rtaCardImage, $imageDirectory);
        $drivingLicenseImageData = moveImageToDirectory($drivingLicenseImage, $imageDirectory);
        $insuranceImageData = moveImageToDirectory($insuranceImage, $imageDirectory);
       

        // Insert driver details into database
        $sql = "INSERT INTO driver_details 
            (`Name`, `Email`, `Address`, `username`, `password`, `phone`, `profile_img`,`passportnum`, 
            `passportIDate`, `passportEDate`,  `Visanum`, `VisaIDate`, `VisaEDate`,  `IdNum`, 
            `IdIDate`, `IdEDate`,  `RtaNum`, `RtaIDate`, `RtaEDate`,  `DLicenseNum`, 
            `DLIDate`, `DLEDate`, `Beneficery_name`, `Iban`, `bankname`, `branchName`, `role`, 
            `InsuranceNo`, `InsuranceIDate`, `InsuranceEDate`) 
            VALUES 
            ('$Name', '$Email', '$Address', '$username', '$password', '$phone', '$profileImageData', '$passportImageData', 
             '$passportIDate', '$passportEDate', '$visaImageData', '$IdIDate', '$IdEDate', 
            '$idCardImageData', '$IdIDate', '$IdEDate', '$rtaCardImageData', '$RtaIDate', '$RtaEDate', 
            '$drivingLicenseImageData', '$DLIDate', '$DLEDate', '$Beneficery_name', '$Iban', '$bankname', 
            '$branchName', 'Driver', '$insuranceImageData', '$InsuranceIDate', '$InsuranceEDate')";

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
                    <h2>Add Driver</h2>
                    <form autocorrect="off" autocomplete="off" id="driverForm" action="add-driver.php" method="POST"
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
                            <label for="address">Address (Optional)</label>
                            <input autocomplete="off" type="text" id="address" name="address" rows="3"></input>
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
                        <div class="form-group">
                            <label for="profileImage">Profile Image (Optional)</label>
                            <input autocomplete="off" type="file" id="profileImage" name="profileImage"
                                accept=".jpg, .png, .jpeg">
                        </div>
                        <div class="form-group">
                            <label for="passportNo">Passport No.<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="file" id="passportNo" name="passportImage"
                                accept=".jpg, .png, .jpeg" required>
                        </div>
                        <div class="form-group">
                            <label for="passportIssueDate">Passport Issue Date<span
                                    style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="date" id="passportIssueDate" name="passportIssueDate"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="passportExpiryDate">Passport Expiry Date<span
                                    style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="date" id="passportExpiryDate" name="passportExpiryDate"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="VisNum">Visa No.<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="file" id="VisNum" name="visaImage" accept=".jpg, .png, .jpeg">
                        </div>
                        
                        <div class="form-group">
                            <label for="IDNum">ID Card No.<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="file" id="IDNum" name="idCardImage" required accept=".jpg, .png, .jpeg">
                        </div>
                        <div class="form-group">
                            <label for="IDIDate">ID Card Issue Date<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="date" id="IDIDate" name="IDIDate" required>
                        </div>
                        <div class="form-group">
                            <label for="IDEDate">ID Card Expiry Date<span style="color: #f90d0d;">*</span></label>
                            <input autocomplete="off" type="date" id="IDEDate" name="IDEDate" required>
                        </div>
                        <div class="form-group">
                            <label for="RtaNum">RTA Card No.</label>
                            <input autocomplete="off" type="file" id="RtaNum" name="rtaCardImage" accept=".jpg, .png, .jpeg" >
                        </div>
                        <div class="form-group">
                            <label for="RtaIDate">RTA Card Issue Date</label>
                            <input autocomplete="off" type="date" id="RtaIDate" name="RtaIDate" >
                        </div>
                        <div class="form-group">
                            <label for="RtaEDate">RTA Card Expiry Date</label>
                            <input autocomplete="off" type="date" id="RtaEDate" name="RtaEDate" >
                        </div>
                        <div class="form-group">
                            <label for="DLicenNum">Driving Licnese No.</label>
                            <input autocomplete="off" type="file" id="DLicenNum" name="drivingLicenseImage" accept=".jpg, .png, .jpeg" >
                        </div>
                        <div class="form-group">
                            <label for="DLIDate">Driving Licnese Issue Date,</label>
                            <input autocomplete="off" type="date" id="DLIDate" name="DLIDate">
                        </div>
                        <div class="form-group">
                            <label for="DLEDate">Driving Licnese Expiry Date</label>
                            <input autocomplete="off" type="date" id="DLEDate" name="DLEDate">
                        </div>
                        <div class="form-group">
                            <label for="InNum">Insurance No.</label>
                            <input type="file" id="InNum" name="insuranceImage" accept=".jpg, .png, .jpeg">
                        </div>
                        <div class="form-group">
                            <label for="InIDate">Insurance Issue Date</label>
                            <input type="date" id="InIDate" name="InsuranceIDate">
                        </div>
                        <div class="form-group">
                            <label for="InEDate">Insurance Expiry Date</label>
                            <input type="date" id="InEDate" name="InsuranceEDate">
                        </div>
                        <fieldset>
                            <legend>Bank Details</legend>
                            <div class="form-group">
                                <label for="beneficiaryName">Beneficiary Name</label>
                                <input type="text" id="beneficiaryName" name="beneficiaryName">
                            </div>
                            <div class="form-group">
                                <label for="iban">IBAN</label>
                                <input type="text" id="iban" name="iban">
                            </div>
                            <div class="form-group">
                                <label for="bankName">Bank Name</label>
                                <input type="text" id="bankName" name="bankName">
                            </div>
                            <div class="form-group">
                                <label for="branchName">Branch Name</label>
                                <input type="text" id="branchName" name="branchName">
                            </div>
                        </fieldset>
                        <label>* indicate required fields</label>
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