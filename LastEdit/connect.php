<!-- <?php
// $conn = mysqli_connect("sg2plzcpnl505474.prod.sin2.secureserver.net","webadmin","~~qw5Eb4]=8*","taxibooking");
?> -->

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "taxibooking";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";