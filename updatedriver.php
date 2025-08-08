<?php
include ('connect.php');
session_start();

// Check if user is logged in
if ($_SESSION['loggedin'] == true) {

    // Check if driver ID is provided in the URL
    if (isset($_GET['id'])) {
        $driver_id = $_GET['id'];

        // Fetch driver details from the database
        $sql = "SELECT * FROM driver_details WHERE id = '$driver_id'";
        $result = mysqli_query($conn, $sql);

       
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Extracting values for form population
            $Name = $row['Name'];
            $Email = $row['Email'];
            $Address = $row['Address'];
            $username = $row['username'];
            $phone = $row['phone'];
            $passportIDate = $row['passportIDate'];
            $passportEDate = $row['passportEDate'];
            $VisaIDate = $row['VisaIDate'];
            $VisaEDate = $row['VisaEDate'];
            $IdIDate = $row['IdIDate'];
            $IdEDate = $row['IdEDate'];
            $RtaIDate = $row['RtaIDate'];
            $RtaEDate = $row['RtaEDate'];
            $DLIDate = $row['DLIDate'];
            $DLEDate = $row['DLEDate'];
            $Beneficery_name = $row['Beneficery_name'];
            $Iban = $row['Iban'];
            $bankname = $row['bankname'];
            $branchName = $row['branchName'];
            $InsuranceIDate = $row['InsuranceIDate'];
            $InsuranceEDate = $row['InsuranceEDate'];
            $profilePicturePath = $row['profile_img'];
            $passportPicturePath = $row['passportnum'];
            $VisaPicturePath = $row['Visanum'];
            $IDcardPicturePath = $row['IdNum'];
            $RtacardPicturePath = $row['RtaNum'];
            $DlicensePicturePath = $row['DLicenseNum'];
            $InsurancePicturePath = $row['InsuranceNo'];

        } else {
            echo "Driver not found.";
            exit;
        }
    } else {
        echo "Driver ID not provided.";
        exit;
    }

    // Handle form submission for updating driver details
    if (isset($_POST['update-driver'])) {
        $Name = $_POST['name'];
        $Email = $_POST['email'];
        $Address = $_POST['address'];
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $passportIDate = $_POST['passportIssueDate'];
        $passportEDate = $_POST['passportExpiryDate'];
        $VisaIDate = $_POST['VisaIDate'];
        $VisaEDate = $_POST['VisaEDate'];
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
        $profileImageData = '';
        if (!empty($profileImageName)) {
            $profileImageData = moveImageToDirectory($profileImage, $imageDirectory);
        } else {
            $profileImageData = $profilePicturePath; // Retain existing path
        }

        $passportImageData = '';
        if (!empty($passportImageName)) {
            $passportImageData = moveImageToDirectory($passportImage, $imageDirectory);
        } else {
            $passportImageData = $passportPicturePath; // Retain existing path
        }

        $visaImageData = '';
        if (!empty($visaImageName)) {
            $visaImageData = moveImageToDirectory($visaImage, $imageDirectory);
        } else {
            $visaImageData = $VisaPicturePath; // Retain existing path
        }

        $idCardImageData = '';
        if (!empty($idCardImageName)) {
            $idCardImageData = moveImageToDirectory($idCardImage, $imageDirectory);
        } else {
            $idCardImageData = $IDcardPicturePath; // Retain existing path
        }

        $rtaCardImageData = '';
        if (!empty($rtaCardImageName)) {
            $rtaCardImageData = moveImageToDirectory($rtaCardImage, $imageDirectory);
        } else {
            $rtaCardImageData = $RtacardPicturePath; // Retain existing path
        }

        $drivingLicenseImageData = '';
        if (!empty($drivingLicenseImageName)) {
            $drivingLicenseImageData = moveImageToDirectory($drivingLicenseImage, $imageDirectory);
        } else {
            $drivingLicenseImageData = $DlicensePicturePath; // Retain existing path
        }

        $insuranceImageData = '';
        if (!empty($insuranceImageName)) {
            $insuranceImageData = moveImageToDirectory($insuranceImage, $imageDirectory);
        } else {
            $insuranceImageData = $InsurancePicturePath; // Retain existing path
        }

        // Update driver details in the database
        $updateSql = "UPDATE driver_details SET Name='$Name', Email='$Email', Address='$Address',  username='$username', phone='$phone', passportIDate='$passportIDate', passportEDate='$passportEDate', VisaIDate='$VisaIDate', VisaEDate='$VisaEDate', IdIDate='$IdIDate', IdEDate='$IdEDate', RtaIDate='$RtaIDate', RtaEDate='$RtaEDate', DLIDate='$DLIDate', DLEDate='$DLEDate', Beneficery_name='$Beneficery_name', Iban='$Iban', bankname='$bankname', branchName='$branchName', InsuranceIDate='$InsuranceIDate', InsuranceEDate='$InsuranceEDate', profile_img='$profileImageData', passportnum='$passportImageData', Visanum='$visaImageData', IdNum='$idCardImageData', RtaNum='$rtaCardImageData', DLicenseNum='$drivingLicenseImageData', InsuranceNo='$insuranceImageData' WHERE id='$driver_id'";

        if (mysqli_query($conn, $updateSql)) {
            echo '<script>alert("Driver details updated successfully.");</script>';
            echo '<script>window.location.href = "view_drivers.php";</script>';
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
} else {
    echo "Please log in first.";
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
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 700px;
            align-items: start;
            align-content: start;
            align-self: flex-start;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin: 10px;
        }

        label {
            display: flex;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        input[type="date"],
        input[type="file"] {
            width: 95%;
            padding: 10px;
            border-radius: 5px;
            background-color: white;

        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }

        fieldset {
            border: 2px solid #ccc;
            border-radius: 5px;
            padding: 13px;
            margin-bottom: 20px;
        }

        legend {
            padding: 0 10px;
            font-weight: bold;
        }
    </style>
    <style>
        .mainCards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .materialIconsOutlined {
            vertical-align: middle;
            line-height: 1px;
            font-size: 35px;
        }
        
        .myCard {
            width: 80%; /* Adjusted width for 4 cards in a row */
            height: 100px;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            text-decoration: none;
            color: black;
            position: relative;
            overflow: hidden;
            background-color: #1abc9c; /* Same color for all cards */
        }
        
        .cardInner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .cardInner > .materialIconsOutlined {
            font-size: 45px;
        }
        
        .card h1 {
            margin: 20px 0;
        }
        
        .card-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        
        .card-popup {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 10px 0;
            font-size: 16px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .myCard:hover .card-popup {
            opacity: 1;
        }
        
        .myCard:hover {
            opacity: 0.8;
            transform: translateY(-5px);
        }
    </style>

<style>
        /* Inline CSS for quick styling, move to styles.css if needed */
        .sidebar-section {
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
            color: black;
            background-color: #1abc9c;
            border-radius: 5px;
        }
        .sidebar-section:hover {
            background-color: #e0e0e0;
        }
        .sidebar-sublist {
            display: none; /* Initially hide all sections */
            padding-left: 20px;
        }
    </style>
    <?php 
    include 'adminAddStyles.php'
    ?>
</head>

<body>
<div class="grid-container">

<?php
            include 'adminSideBar.php';
?>
            <div id="container">
                <div class="container">
                    <h2>Edit Driver</h2>
                    <form method="POST" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="name">Name<span style="color: #f90d0d;">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo htmlspecialchars($Name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email (Optional)</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($Email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address (Optional)</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                required><?php echo htmlspecialchars($Address); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="username">Username (Auto Generated)<span
                                    style="color: #f90d0d;">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        

                        <div class="form-group">
                            <label for="phone">Phone Number<span style="color: #f90d0d;">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                       
                        <div class="form-group">
                            <label for="profileImage">Profile Image (Optional)</label>
                            <input type="file" class="form-control-file" id="profileImage" name="profileImage">
                                <img src="<?php echo $profilePicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="Profile Image">
                        </div>
                        <div class="form-group">
                            <label for="passportNo">Passport No.<span style="color: #f90d0d;">*</span></label>
                            <input type="file" class="form-control-file" id="passportImage" name="passportImage">
                            <?php if (!empty($passportPicturePath)): ?>
                                <img src="<?php echo $passportPicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="Passport Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="passportIssueDate">Passport Issue Date<span
                                    style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="passportIssueDate" name="passportIssueDate"
                                value="<?php echo htmlspecialchars($passportIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="passportExpiryDate">Passport Expiry Date<span
                                    style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="passportExpiryDate" name="passportExpiryDate"
                                value="<?php echo htmlspecialchars($passportEDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="VisNum">Visa No.<span style="color: #f90d0d;">*</span></label>
                            <input type="file" class="form-control-file" id="visaImage" name="visaImage">
                            <?php if (!empty($VisaPicturePath)): ?>
                                <img src="<?php echo $VisaPicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="Visa Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="VisaIDate">Visa Issue Date<span style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="VisaIDate" name="VisaIDate"
                                value="<?php echo htmlspecialchars($VisaIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="VisaEDate">Visa Expiry Date<span style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="VisaEDate" name="VisaEDate"
                                value="<?php echo htmlspecialchars($VisaEDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="IDNum">ID Card No.<span style="color: #f90d0d;">*</span></label>
                            <input type="file" class="form-control-file" id="idCardImage" name="idCardImage">
                            <?php if (!empty($IDcardPicturePath)): ?>
                                <img src="<?php echo $IDcardPicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="ID Card Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="IDIDate">ID Card Issue Date<span style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="IDIDate" name="IDIDate"
                                value="<?php echo htmlspecialchars($IdIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="IDEDate">ID Card Expiry Date<span style="color: #f90d0d;">*</span></label>
                            <input type="date" class="form-control" id="IDEDate" name="IDEDate"
                                value="<?php echo htmlspecialchars($IdEDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="RtaNum">RTA Card No.</label>
                            <input type="file" class="form-control-file" id="rtaCardImage" name="rtaCardImage">
                            <?php if (!empty($RtacardPicturePath)): ?>
                                <img src="<?php echo $RtacardPicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="RTA Card Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="RtaIDate">RTA Card Issue Date</label>
                            <input type="date" class="form-control" id="RtaIDate" name="RtaIDate"
                                value="<?php echo htmlspecialchars($RtaIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="RtaEDate">RTA Card Expiry Date</label>
                            <input type="date" class="form-control" id="RtaEDate" name="RtaEDate"
                                value="<?php echo htmlspecialchars($RtaEDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="DLicenNum">Driving Licnese No.</label>
                            <input type="file" class="form-control-file" id="drivingLicenseImage"
                                name="drivingLicenseImage">
                            <?php if (!empty($DlicensePicturePath)): ?>
                                <img src="<?php echo $DlicensePicturePath; ?>"
                                    class="img-thumbnail mt-2" style="max-width: 200px;" alt="Driving License Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="DLIDate">Driving Licnese Issue Date,</label>
                            <input type="date" class="form-control" id="DLIDate" name="DLIDate"
                                value="<?php echo htmlspecialchars($DLIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="DLEDate">Driving Licnese Expiry Date</label>
                            <input type="date" class="form-control" id="DLEDate" name="DLEDate"
                                value="<?php echo htmlspecialchars($DLEDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="InNum">Insurance No.</label>
                            <input type="file" class="form-control-file" id="insuranceImage" name="insuranceImage">
                            <?php if (!empty($InsurancePicturePath)): ?>
                                <img src="<?php echo $InsurancePicturePath; ?>" class="img-thumbnail mt-2"
                                    style="max-width: 200px;" alt="Insurance Image">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="InIDate">Insurance Issue Date</label>
                            <input type="date" class="form-control" id="InsuranceIDate" name="InsuranceIDate"
                                value="<?php echo htmlspecialchars($InsuranceIDate); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="InEDate">Insurance Expiry Date</label>
                            <input type="date" class="form-control" id="InsuranceEDate" name="InsuranceEDate"
                                value="<?php echo htmlspecialchars($InsuranceEDate); ?>" required>
                        </div>
                        <fieldset>
                            <legend>Bank Details</legend>
                            <div class="form-group">
                                <label for="beneficiaryName">Beneficiary Name</label>
                                <input type="text" class="form-control" id="beneficiaryName" name="beneficiaryName"
                                    value="<?php echo htmlspecialchars($Beneficery_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="iban">IBAN</label>
                                <input type="text" class="form-control" id="iban" name="iban"
                                    value="<?php echo htmlspecialchars($Iban); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="bankName">Bank Name</label>
                                <input type="text" class="form-control" id="bankName" name="bankName"
                                    value="<?php echo htmlspecialchars($bankname); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="branchName">Branch Name</label>
                                <input type="text" class="form-control" id="branchName" name="branchName"
                                    value="<?php echo htmlspecialchars($branchName); ?>" required>
                            </div>
                        </fieldset>
                        <label>* indicate required fields</label>
                        <!-- Add more fields as needed -->
                        <button type="submit" class="button" name="update-driver">Update Driver</button>
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

</body>

</html>