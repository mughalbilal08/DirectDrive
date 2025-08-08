<?php
// Include database connection
include('connect.php');
session_start();

// Check if user is logged in
if ($_SESSION['loggedin'] == true) {
    // Check if driver id is provided in the URL
    if (isset($_GET['id'])) {
        // Sanitize and validate input
        $driverId = mysqli_real_escape_string($conn, $_GET['id']);

        // SQL query to delete driver from driver_details table
        $sql_delete_driver = "DELETE FROM driver_details WHERE id = '$driverId'";
        $result_delete_driver = mysqli_query($conn, $sql_delete_driver);

        if ($result_delete_driver) {
            // Redirect back to view drivers page after successful deletion
            header("Location: view_drivers.php");
            exit();
        } else {
            // Error handling if deletion fails
            echo "Error deleting driver. Please try again.";
        }
    } else {
        // Error handling if driver id is not provided
        echo "Driver id not provided.";
    }
} else {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
