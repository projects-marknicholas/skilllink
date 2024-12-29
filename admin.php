<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$user_count_query = "SELECT COUNT(*) AS total_job FROM jobs WHERE is_admin = 0";
$user_count_result = $conn->query($user_count_query);
$user_count = $user_count_result->fetch_assoc()['total_jobs'];

$shop_count_query = "SELECT COUNT(*) AS total_employers FROM employers";
$shop_count_result = $conn->query($shop_count_query);
$shop_count = $shop_count_result->fetch_assoc()['total_employers'];

$order_count_query = "SELECT COUNT(*) AS total_employee FROM employee";
$order_count_result = $conn->query($order_count_query);
$order_count = $order_count_result->fetch_assoc()['total_employee'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="admin_homepage.css"> 
</head>
<body>
    <div class="sidebar">
        <h1>Welcome Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
        <a href="manage_users.php">Manage Jobs</a>
        <a href="manage_orders.php">Manage Employers</a>
        <a href="manage_products.php">Manage Employee</a> 
        <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="stats-container">
            <div class="stat-card">
                <h2>Total Jobs</h2>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat-card">
                <h2>Total Employers</h2>
                <p><?php echo $shop_count; ?></p>
            </div>
            <div class="stat-card">
            <h2>Total Employee</h2>
            <p><?php echo $order_count; ?></p>
            
        </div>
        </div>
        <div class="logo">
            <img src="uploads/skilllinkicon3.png" alt="Logo" class="logo-img">
        </div>
    </div>
</body>
</html>
<style>.logo {
    display: flex;
    justify-content: center; 
    align-items: center; 
    height: 100%; 
}

.logo-img {
    max-width: 100%; 
    height: auto;
}
</style>