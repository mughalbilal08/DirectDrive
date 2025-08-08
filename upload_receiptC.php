<?php
include('connect.php');
session_start();

// Include TCPDF library
require_once('tcpdf/tcpdf.php');

// Check if user is logged in and is a customer
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$name = $_SESSION['name'];

// Use session's selected plan for subscription type
$subscription_type = $_SESSION['selected_plan'] ?? 'basic';

// Debug: Log the subscription type being used
error_log("Subscription type from session in upload_receiptC.php: $subscription_type");

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

// Define plan prices
$plan_prices = [
    'basic' => 0,
    'standard' => 15,
    'premium' => 25
];
$plan_price = $plan_prices[$subscription_type] ?? 0;

// Generate PDF receipt using TCPDF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the absolute path to the receipts directory
    $upload_dir = __DIR__ . '/receiptsC/';
    
    // Debug: Log the directory path
    error_log("Receipts directory: " . $upload_dir);

    // Create the directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Failed to create directory: " . $upload_dir);
            echo '<script>alert("Failed to create receipts directory. Please contact support."); window.location.href = "customer_dashboard.php";</script>';
            exit();
        }
    }

    // Check if the directory is writable
    if (!is_writable($upload_dir)) {
        error_log("Directory is not writable: " . $upload_dir);
        echo '<script>alert("Receipts directory is not writable. Please contact support."); window.location.href = "customer_dashboard.php";</script>';
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
    <p><strong>Customer Name:</strong> ' . htmlspecialchars($name) . '</p>
    <p><strong>User ID:</strong> ' . htmlspecialchars($user_id) . '</p>
    <p><strong>Plan Selected:</strong> ' . htmlspecialchars(ucfirst($subscription_type)) . '</p>
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
        echo '<script>alert("Failed to generate receipt: ' . addslashes($e->getMessage()) . '"); window.location.href = "customer_dashboard.php";</script>';
        exit();
    }

    // Log the receipt in the database for admin review
    $relative_file_path = 'receiptsC/' . $file_name; // Store relative path in DB
    $sql = "INSERT INTO receipts_uploadC (user_id, file_path, upload_date, status) VALUES (?, ?, NOW(), 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $relative_file_path);

    if ($stmt->execute()) {
        // Update subscription_type in user_accounts
        $sql = "UPDATE user_accounts SET subscription_type = ? WHERE id = ? AND role = 'customer'";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("si", $subscription_type, $user_id);
        if ($stmt_update->execute()) {
            echo '<script>alert("Receipt generated and submitted for admin approval."); window.location.href = "customer_dashboard.php";</script>';
        } else {
            error_log("Failed to update subscription_type in user_accounts: " . $stmt_update->error);
            echo '<script>alert("Failed to update subscription type. Please try again."); window.location.href = "customer_dashboard.php";</script>';
        }
        $stmt_update->close();
    } else {
        error_log("Database insert failed: " . $stmt->error);
        echo '<script>alert("Failed to log receipt in database. Please try again."); window.location.href = "customer_dashboard.php";</script>';
    }
    $stmt->close();
} else {
    echo '<script>alert("Invalid request."); window.location.href = "customer_dashboard.php";</script>';
}
?>