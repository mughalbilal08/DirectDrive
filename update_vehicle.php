<?php
include('connect.php');
session_start();

if ($_SESSION['loggedin'] == true) {
    // Fetch vehicle details based on ID
    if (isset($_GET['id'])) {
        $vehicle_id = $_GET['id'];
        $sql = "SELECT * FROM vehicle_details WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $vehicle_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            // Assign fetched values to variables
            $Name = $row['Name'];
            $Number = $row['Number'];
            $Year = $row['Year'];
            $Chasis = $row['Chasis'];
            $Picture = $row['Picture'];
            $InNumber = $row['InNumber'];
            $InCompany = $row['InCompany'];
            $InIDate = $row['InIDate'];
            $InEDate = $row['InEDate'];
            $RIIDate = $row['RIIDate'];
            $RIEDate = $row['RIEDate'];
            $RTAPNum = $row['RTAPNum'];
            $RTAPIDate = $row['RTAPIDate'];
            $RTAPEDate = $row['RTAPEDate'];
        } else {
            echo "Vehicle not found.";
            exit; // Exit if vehicle ID not found
        }
    } else {
        echo "Invalid request.";
        exit; // Exit if ID not provided
    }

    // Process form submission for updating vehicle details
    if (isset($_POST['update-vehicle'])) {
        $Name = $_POST['name'];
        $Number = $_POST['number'];
        $Year = $_POST['year'];
        $Chasis = $_POST['chasis'];
        $InNumber = $_POST['InsNum'];
        $InCompany = $_POST['InCname'];
        $InIDate = $_POST['InIDate'];
        $InEDate = $_POST['InEDate'];
        $RIIDate = $_POST['RegIDate'];
        $RIEDate = $_POST['RegEDate'];
        $RTAPNum = $_POST['RtaNum'];
        $RTAPIDate = $_POST['RtaIDate'];
        $RTAPEDate = $_POST['RtaEDate'];

        // Check if a new picture file is uploaded
        if (!empty($_FILES['picture']['name'])) {
            $file_name = $_FILES['picture']['name'];
            $file_tmp = $_FILES['picture']['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Validate file extension
            if (in_array($file_ext, array('jpg', 'jpeg', 'png'))) {
                $image_data = base64_encode(file_get_contents($file_tmp));
                $Picture = $file_name; // Update picture filename
                file_put_contents("vehicleimages/".$file_name, base64_decode($image_data)); // Save file on server
            } else {
                echo '<script type="text/javascript">alert("Only Image files are allowed(.jpg, .png, .jpeg)");
                    </script>';
                // Handle error if file extension is not allowed
            }
        }

        // Update the vehicle details in the database
        $sql_update = "UPDATE vehicle_details SET 
            Name = '$Name', 
            Number = '$Number', 
            Year = '$Year', 
            Chasis = '$Chasis', 
            Picture = '$Picture', 
            InNumber = '$InNumber', 
            InCompany = '$InCompany', 
            InIDate = '$InIDate', 
            InEDate = '$InEDate', 
            RIIDate = '$RIIDate', 
            RIEDate = '$RIEDate', 
            RTAPNum = '$RTAPNum', 
            RTAPIDate = '$RTAPIDate', 
            RTAPEDate = '$RTAPEDate' 
            WHERE id = $vehicle_id";

        if (mysqli_query($conn, $sql_update)) {
            echo '<script>alert("Vehicle updated successfully"); window.location.href = "view_vehicles.php";</script>';
            // Redirect to view vehicles page after successful update
        } else {
            echo "Error updating vehicle: " . mysqli_error($conn);
            // Handle error if update query fails
        }
    }
} else {
    header("location: login.php");
    // Redirect to login page if user is not logged in
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


    

</head>

<body>
<div class="grid-container">

<?php
            include 'adminSideBar.php';
?>
           
            <div id="container">
                <div class="container">
                    <h2>Edit Vehicle</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                          <label for="name">Name<span style="color: #f90d0d;">*</span> </label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $Name; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="number">Number<span style="color: #f90d0d;">*</span> </label>
                <input type="text" class="form-control" id="number" name="number" value="<?php echo $Number; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="year">Year<span style="color: #f90d0d;">*</span> </label>
                <input type="number" class="form-control" id="year" name="year" value="<?php echo $Year; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="chasis">Chassis Number (Optional)</label>
                <input type="text" class="form-control" id="chasis" name="chasis" value="<?php echo $Chasis; ?>">
                        </div>
                        <div class="form-group">
                          <label for="picture">Picture (Optional)</label>
                <input type="file" class="form-control-file" id="picture" name="picture">
                        </div>
                        <div class="form-group">
                          <label for="InsNum">Insurance Number (Optional)</label>
                <input type="text" class="form-control" id="InsNum" name="InsNum" value="<?php echo $InNumber; ?>">
                        </div>
                        <div class="form-group">
                          <label for="InCname">Insurance Company Name (Optional)</label>
                <input type="text" class="form-control" id="InCname" name="InCname" value="<?php echo $InCompany; ?>">
                        </div>
                        <div class="form-group">
                          <label for="InIDate">Insurance Issue date<span style="color: #f90d0d;">*</span> </label>
                <input type="date" class="form-control" id="InIDate" name="InIDate" value="<?php echo $InIDate; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="InEDate">Insurance Expiry Date<span style="color: #f90d0d;">*</span> </label>
                <input type="date" class="form-control" id="InEDate" name="InEDate" value="<?php echo $InEDate; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="RegIDate">Registration Issue Date<span style="color: #f90d0d;">*</span> </label>
                <input type="date" class="form-control" id="RegIDate" name="RegIDate" value="<?php echo $RIIDate; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="RegEDate">Registration Expiry date<span style="color: #f90d0d;">*</span> </label>
                <input type="date" class="form-control" id="RegEDate" name="RegEDate" value="<?php echo $RIEDate; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="RtaNum">RTA Permit No. (Optional)</label>
                <input type="number" class="form-control" id="RtaNum" name="RtaNum" value="<?php echo $RTAPNum; ?>">
                        </div>
                        <div class="form-group">
                          <label for="RtaIDate">RTA Issue Date (Optional)</label>
                <input type="date" class="form-control" id="RtaIDate" name="RtaIDate" value="<?php echo $RTAPIDate; ?>">
                        </div>
                        <div class="form-group">
                          <label for="RtaEDate">RTA Expiry Date(Optional)</label>
                <input type="date" class="form-control" id="RtaEDate" name="RtaEDate" value="<?php echo $RTAPEDate; ?>">
                        </div>
                        <label >* indicate required fileds</label>
                        <!-- Add more fields as needed -->
            <button type="submit" name="update-vehicle" class="button">Update Vehicle</button>
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
