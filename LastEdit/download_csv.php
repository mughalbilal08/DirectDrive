<?php
session_start();

// Check if data is set in the session
if (isset($_SESSION['result_data'])) {
    $data = $_SESSION['result_data'];

    // Set headers to force download the file as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="driver_payments.csv"');

    // Open PHP output stream for writing
    $output = fopen('php://output', 'w');

    // Output the column headers
    fputcsv($output, ['Driver ID', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', 'Friday', 'Without-Friday']);

    // Output the data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);

    // Clear the session variable
    unset($_SESSION['result_data']);
}
?>
