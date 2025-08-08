<?php
include('connect.php');
session_start();
if($_SESSION['loggedin']==true){
    
    $sql0="select * from driver_details where role ='Driver'";
    $result0 = mysqli_query($conn,$sql0);
    $numberofDrivers = mysqli_num_rows($result0);
    $sql1="select * from user_accounts where role ='customer'";
    $result1 = mysqli_query($conn,$sql1);
    $numberofCustomers = mysqli_num_rows($result1);
    $sql2="SELECT SUM(total) AS total FROM expenses_details;";
    $result2 = mysqli_query($conn,$sql2);
    $row = mysqli_fetch_assoc($result2);
    $totalExpenses = $row['total'];

    if(isset($_GET['logout'])){
        $_SESSION['loggedin']=false;
        $_SESSION['email']= "";
        $_SESSION['name']="";
        session_unset();
        session_destroy();
        header("location:index.html");
    }elseif(isset($_POST['add-staff'])){
        $Name =$_POST['name'];
        $Email =$_POST['email'];
        $username=$_POST['username'];
        $password=$_POST['password'];
        $phone = $_POST['phone'];
        $passportnum = $_POST['passportNo'];
        $passportIDate = $_POST['passportIssueDate'];
        $passportEDate=$_POST['passportExpiryDate'];
        $Visanum = $_POST['VisaNum'];
        $IdNum=$_POST['IDNum'];
        $IdIDate=$_POST['IDIDate'];
        $IdEDate=$_POST['IDEDate'];
        $InNum = $_POST['InNum'];
        $InIDate = $_POST['InIDate'];
        $InEDate = $_POST['InEDate'];
        $Beneficery_name=$_POST['beneficiaryName'];
        $Iban=$_POST['iban'];
        $bankname=$_POST['bankName'];
        $branchName=$_POST['branchName'];
        $file_name = $_FILES['profileImage']['name'];
        $file_tmp = $_FILES['profileImage']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if(in_array($file_ext, array('jpg', 'jpeg', 'png')) && !empty($file_name)) {
                $image_data = base64_encode(file_get_contents($file_tmp));
            }elseif(empty($file_name)){
                
            } else {
                echo '<script type="text/javascript">alert("Only Image files are allowed(.jpg,.png,.jpeg)");
               </script>';}
               $sql="INSERT INTO `staff_details` (`name`, `email`, `password`, `username`, `phone`, `benefiecery_name`, 
               `bankname`, `branchname`, `iban`, `visaNum`, `VisaIDate`, `VisaEDate`, `IDNum`, `IDIDate`, `IDEDate`,
                `PassportNum`, `PassportIDate`, `PassportEDate`, `InNumber`, `InIDate`, `InEDate`, `profile_picture`,`role`)
                 VALUES ('$Name', '$Email', '$password', '$username', '$phone', '$Beneficery_name', '$bankname', '$branchName',
                  '$Iban', '$Visanum', '$IdIDate', '$IdEDate', '$IdNum', '$IdIDate', '$IdEDate', '$passportnum', '$passportIDate',
                   '$passportEDate', '$InNum', '$InIDate', '$InEDate', '$file_name','Admin')";
              $res = mysqli_query($conn,$sql);
              if($res= true){
              file_put_contents("vehicleimages/".$file_name, base64_decode($image_data));
              echo '<script type="text/javascript">alert("Submitted Successfully");
                        window.location.href = "add-staff.php";
              </script>';
              }else{
                  echo '<script type="text/javascript">alert("Not Submitted Please try again");
                         </script>';
              }

    }
           
}else{
    header("location:login.php");
}
?>

<!DOCTYPE html>
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
                    <h2>Add Staff</h2>
                    <form id="driverForm" action="add-staff.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                          <label for="name">Name<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                          <label for="email">Email (Optional)</label>
                          <input type="email" id="email" name="email">
                        </div>
                        <div class="form-group">
                          <label for="username">Username (Auto Generated)<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="username" name="username" onclick="generateUsername()" required>
                        </div>
                        <div class="form-group">
                          <label for="password">Password<span style="color: #f90d0d;">*</span></label>
                          <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                          <label for="phone">Phone Number<span style="color: #f90d0d;">*</span></label>
                          <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                          <label for="profileImage">Profile Image (Optional)</label>
                          <input type="file" id="profileImage" name="profileImage">
                        </div>
                        <div class="form-group">
                          <label for="passportNo">Passport No.<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="passportNo" name="passportNo" required>
                        </div>
                        <div class="form-group">
                          <label for="passportIssueDate">Passport Issue Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="passportIssueDate" name="passportIssueDate" required>
                        </div>
                        <div class="form-group">
                          <label for="passportExpiryDate">Passport Expiry Date <span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="passportExpiryDate" name="passportExpiryDate" required>
                        </div>
                        <div class="form-group">
                          <label for="VisaNum">Visa No.<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="VisaNum" name="VisaNum" required>
                        </div>
                        <div class="form-group">
                          <label for="IDNum">ID Card No.<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="IDNum" name="IDNum" required>
                        </div>
                        <div class="form-group">
                          <label for="IDIDate">ID Card Issue Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="IDIDate" name="IDIDate" required>
                        </div>
                        <div class="form-group">
                          <label for="IDEDate">ID Card Expiry Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="IDEDate" name="IDEDate" required>
                        </div>
                        <div class="form-group">
                          <label for="InNum">Insurance No.<span style="color: #f90d0d;">*</span></label>
                          <input type="text" id="InNum" name="InNum" required>
                        </div>
                        <div class="form-group">
                          <label for="InIDate">Insurance Issue Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="InIDate" name="InIDate" required>
                        </div>
                        <div class="form-group">
                          <label for="InEDate">Insurance Expiry Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" id="InEDate" name="InEDate" required>
                        </div>
                        <fieldset>
                          <legend>Bank Details</legend>
                          <div class="form-group">
                            <label for="beneficiaryName">Beneficiary Name<span style="color: #f90d0d;">*</span></label>
                            <input type="text" id="beneficiaryName" name="beneficiaryName" required>
                          </div>
                          <div class="form-group">
                            <label for="iban">IBAN<span style="color: #f90d0d;">*</span></label>
                            <input type="text" id="iban" name="iban" required>
                          </div>
                          <div class="form-group">
                            <label for="bankName">Bank Name<span style="color: #f90d0d;">*</span></label>
                            <input type="text" id="bankName" name="bankName" required>
                          </div>
                          <div class="form-group">
                            <label for="branchName">Branch Name<span style="color: #f90d0d;">*</span></label>
                            <input type="text" id="branchName" name="branchName" required>
                          </div>
                        </fieldset>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="add-staff" class="button">Submit</button>
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
     var username =  nameWithoutSpaces+randomString ;
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