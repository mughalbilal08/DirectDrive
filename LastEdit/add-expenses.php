<?php
include('connect.php');
session_start();
if($_SESSION['loggedin']==true){
     $aid = $_SESSION['id'];
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
    }elseif(isset($_POST['add-expenses'])){
        $fuel=$_POST['fuel'];
        	$salik=$_POST['salik'];	$Office=$_POST['office'];
            $License=$_POST['lic'];
            $Bills =$_POST['Bills'];
            $Staff =$_POST['staff'];
            $Salaries=$_POST['saleries'];
            $Depreciation=$_POST['depri'];
            $reserve =$_POST['reserve'];
            $rent =$_POST['rent'];
            $date =$_POST['date'];
            $sum = $fuel+$salik+$Office+$License+$Bills+$Staff+$Salaries+$Depreciation+$reserve+$rent;
               $sql="INSERT INTO `expenses_details` (`add_by`,`fuel`, `salik`, `Office`, `License`, `Bills`, `Staff`, `Salaries`, 
               `Depreciation`, `reserve`, `rent`, `total`,`type`,`date`) VALUES ('Admin','$fuel', '$salik','$Office', '$License', '$Bills', '$Staff', '$Salaries', '$Depreciation',
                '$reserve', ' $rent','$sum','Admin expense','$date');";
            $res = mysqli_query($conn,$sql);
              if($res= true){
               echo '<script type="text/javascript">alert("Submitted Successfully");
                        window.location.href = "add-expenses.php";
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
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #1abc9c;
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

        select {
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
                flex: 1 1 100%;
            }

            button {
                flex: 1 1 100%;
            }
        }
    </style>
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
          display: block;
          margin-bottom: 5px;
      }

      input[type="text"],
      input[type="email"],
      input[type="password"],
      input[type="tel"] ,input[type="date"],
      input[type="file"],input[type='number']
      {
          width: 95%;
          padding: 10px;
           border-radius: 5px;
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


</head>

<body>
<div class="grid-container">
        
<?php
            include 'adminSideBar.php';
?>



            <div id="container">
                <div class="container">
                    <h2>Add Expenses</h2>
                    <form id="driverForm" method="POST" action="add-expenses.php">
                        <div class="form-group">
                          <label for="fuel">Fuel<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="fuel" name="fuel" required>
                        </div>
                        <div class="form-group">
                          <label for="Salik">Salik<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="salik" name="salik" required>
                        </div>
                        <div class="form-group">
                          <label for="office">Office<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="office" name="office" required>
                        </div>
                        <div class="form-group">
                          <label for="lic">License<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="lic" name="lic" required>
                        </div>
                        <div class="form-group">
                          <label for="Bills">Bills<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="Bills" name="Bills" required>
                        </div>
                        <div class="form-group">
                          <label for="staff">Staff<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="staff" name="staff" required>
                        </div>
                        <div class="form-group">
                          <label for="saleries">Salaries<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="saleries" name="saleries" required>
                        </div>
                        <div class="form-group">
                          <label for="depri">Depreciation<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="depri" name="depri" required>
                        </div>
                        <div class="form-group">
                          <label for="reserve">Reserve<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="reserve" name="reserve" required>
                        </div>
                        <div class="form-group">
                          <label for="rent">Rent<span style="color: #f90d0d;">*</span></label>
                          <input type="number" value="0" id="rent" name="rent" required>
                        </div>
                        <div class="form-group">
                          <label for="date">Date<span style="color: #f90d0d;">*</span></label>
                          <input type="date" value="" id="date" name="date" required>
                        </div>
                       <label>* indicates required fields</label>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="add-expenses" class="button">Submit</button>
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