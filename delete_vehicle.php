<?php
// Include database connection
include('connect.php');
session_start();

// Check if user is logged in
if ($_SESSION['loggedin'] == true) {
    // Check if Vehicle id is provided in the URL
    if (isset($_GET['id'])) {
        // Sanitize and validate input
        $vehicleId = mysqli_real_escape_string($conn, $_GET['id']);

        // SQL query to delete Vehicle from vehicle_details table
        $sql_delete_vehicle = "DELETE FROM vehicle_details WHERE id = '$vehicleId'";
        $result_delete_vehicle = mysqli_query($conn, $sql_delete_vehicle);

        if ($result_delete_vehicle) {
            // Set success message in session variable
            $_SESSION['delete_success'] = "Vehicle deleted successfully.";

            // Redirect back to view Vehicles page after successful deletion
            header("Location: view_vehicles.php");
            exit();
        } else {
            // Error handling if deletion fails
            echo "Error deleting Vehicle. Please try again.";
        }
    } else {
        // Error handling if Vehicle id is not provided
        echo "Vehicle id not provided.";
    }
} else {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
