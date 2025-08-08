<?php
include 'connect.php';
session_start();

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscription_id'])) {
    $subscription_id = $_POST['subscription_id'];
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+30 days'));

    // Set all previous subscriptions to expired
    $conn->query("UPDATE user_subscriptions 
                  SET status = 'expired' 
                  WHERE user_id = $user_id AND user_type = 'driver'");

    // Insert new subscription
    $stmt = $conn->prepare("INSERT INTO user_subscriptions 
        (user_id, user_type, subscription_id, start_date, end_date, status) 
        VALUES (?, 'driver', ?, ?, ?, 'active')");
    $stmt->bind_param("iiss", $user_id, $subscription_id, $start_date, $end_date);
    $stmt->execute();

    $success_message = "Subscription activated successfully!";
}

// Fetch available driver subscriptions
$plans = $conn->query("SELECT * FROM subscriptions WHERE user_type = 'driver'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Subscriptions</title>
    <style>
        .plan {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            margin: 10px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <h2>Choose Your Subscription Plan</h2>

    <?php if ($success_message): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>

    <?php while($plan = $plans->fetch_assoc()): ?>
        <div class="plan">
            <h3><?= htmlspecialchars($plan['title']) ?></h3>
            <p><strong>Price:</strong> $<?= $plan['price'] ?></p>
            <p><?= htmlspecialchars($plan['features']) ?></p>
            <form method="POST">
                <input type="hidden" name="subscription_id" value="<?= $plan['id'] ?>">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>
