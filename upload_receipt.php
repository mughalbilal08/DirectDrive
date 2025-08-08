<?php
include('connect.php');
session_start();

// Include TCPDF library
require_once('tcpdf/tcpdf.php');

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Determine user role
$user_role = $_SESSION['role'] ?? 'driver';

// Restrict access to only drivers
if ($user_role !== 'driver') {
    echo '<script>alert("Access denied. This page is for drivers only."); window.location.href = "login.php";</script>';
    exit();
}

// Use the same session variable for ID as driverdashboard.php
$user_id = $_SESSION['id'];

// Check if a plan is selected
if (!isset($_SESSION['selected_plan'])) {
    echo '<script>alert("No plan selected. Please select a plan first."); window.location.href = "subscriptions.php";</script>';
    exit();
}
$selected_plan = $_SESSION['selected_plan'];

// Define plan prices
$plan_prices = [
    'basic' => 0,
    'standard' => 10,
    'premium' => 20
];
$plan_price = $plan_prices[$selected_plan] ?? 0;

// Fetch user details
$sql = "SELECT name, email FROM driver_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_name = $user['name'] ?? 'Unknown';
$user_email = $user['email'] ?? 'N/A';
$stmt->close();

// Fetch payment credentials
$sql = "SELECT card_holder, card_number, expiry_date FROM payment_credentials WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$credentials = $result->fetch_assoc();
$card_holder = $credentials['card_holder'] ?? 'N/A';
$card_number = $credentials['card_number'] ?? 'N/A';
$expiry_date = $credentials['expiry_date'] ?? 'N/A';
$stmt->close();

// Generate PDF receipt using TCPDF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_receipt'])) {
    // Define the absolute path to the receipts directory
    $upload_dir = __DIR__ . '/receipts/';
    
    // Debug: Log the directory path
    error_log("Receipts directory: " . $upload_dir);

    // Create the directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Failed to create directory: " . $upload_dir);
            echo '<script>alert("Failed to create receipts directory. Please contact support."); window.location.href = "driverdashboard.php";</script>';
            exit();
        }
    }

    // Check if the directory is writable
    if (!is_writable($upload_dir)) {
        error_log("Directory is not writable: " . $upload_dir);
        echo '<script>alert("Receipts directory is not writable. Please contact support."); window.location.href = "driverdashboard.php";</script>';
        exit();
    }

    // Define the file path
    $file_name = 'receipt_' . $user_id . '_' . time() . '.pdf';
    $file_path = $upload_dir . $file_name;

    // Debug: Log the file path
    error_log("File path: " . $file_path);

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Direct Drive');
    $pdf->SetTitle('Payment Receipt');
    $pdf->SetSubject('Subscription Payment Receipt');
    $pdf->SetKeywords('Receipt, Subscription, Direct Drive');

    // Remove default header/footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add a page
    $pdf->AddPage();

    // Create the receipt content
    $html = '
    <h1 style="text-align: center;">Direct Drive</h1>
    <h2 style="text-align: center;">Payment Receipt</h2>
    <hr>
    <p><strong>Driver Name:</strong> ' . htmlspecialchars($user_name) . '</p>
    <p><strong>Email:</strong> ' . htmlspecialchars($user_email) . '</p>
    <p><strong>Plan Selected:</strong> ' . htmlspecialchars(ucfirst($selected_plan)) . '</p>
    <p><strong>Amount:</strong> $' . htmlspecialchars($plan_price) . '/month</p>
    <p><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
    <hr>
    <h3>Payment Details</h3>
    <p><strong>Card Holder:</strong> ' . htmlspecialchars($card_holder) . '</p>
    <p><strong>Card Number:</strong> **** **** **** ' . substr($card_number, -4) . '</p>
    <p><strong>Expiry Date:</strong> ' . htmlspecialchars($expiry_date) . '</p>
    <hr>
    <p style="text-align: center;">Thank you for your subscription!</p>
    <p style="text-align: center;">This receipt is pending admin approval.</p>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Save the PDF to the server
    try {
        $pdf->Output($file_path, 'F');
    } catch (Exception $e) {
        error_log("TCPDF Error: " . $e->getMessage());
        echo '<script>alert("Failed to generate receipt: ' . addslashes($e->getMessage()) . '"); window.location.href = "driverdashboard.php";</script>';
        exit();
    }

    // Update subscription_type in driver_details
    $sql = "UPDATE driver_details SET subscription_type = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $selected_plan, $user_id);
    if (!$stmt->execute()) {
        error_log("Failed to update subscription_type in driver_details: " . $stmt->error);
        echo '<script>alert("Failed to update subscription type. Please try again."); window.location.href = "driverdashboard.php";</script>';
        exit();
    }
    $stmt->close();

    // Log the receipt in the database for admin review
    $relative_file_path = 'receipts/' . $file_name; // Store relative path in DB
    $sql = "INSERT INTO receipt_uploads (user_id, file_path, upload_date, status) VALUES (?, ?, NOW(), 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $relative_file_path);

    if ($stmt->execute()) {
        echo '<script>alert("Receipt generated and submitted for admin approval."); window.location.href = "driverdashboard.php";</script>';
    } else {
        error_log("Database insert failed: " . $stmt->error);
        echo '<script>alert("Failed to log receipt in database. Please try again."); window.location.href = "driverdashboard.php";</script>';
    }
    $stmt->close();
} else {
    echo '<script>alert("Invalid request."); window.location.href = "driverdashboard.php";</script>';
}
?>