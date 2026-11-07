<?php
date_default_timezone_set('Asia/Colombo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do Activities</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    
<header class="main-header">
    <div class="logo-container">
        <img src="images/logo.png" alt="Logo" class="round-logo" onerror="this.src='https://via.placeholder.com/50'">
        <h1>To-Do Activities</h1>
    </div>

    <!-- Navigation bar will only be displayed if the user is logged in -->
    <?php if(isset($_SESSION['user_id'])): ?>
    <nav class="nav-bar">
        <ul>
            <li><a href="home.php">01 - Home</a></li>
            <li><a href="add_work.php">02 - Add Work</a></li>
            <li><a href="show_work.php">03 - Show Work</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>
    <?php endif; ?>
</header>
<main>

<?php
// Pseudo-Cron Trigger for InfinityFree
if (isset($_SESSION['user_id'])) {
    
    if (!isset($_SESSION['last_reminder_run']) || (time() - $_SESSION['last_reminder_run']) > 300) {
        $_SESSION['last_reminder_run'] = time();
        include_once __DIR__ . '/../cron_reminder.php';
    }

    
    $today_str = date('Y-m-d');
    if (!isset($_SESSION['last_daily_run']) || $_SESSION['last_daily_run'] !== $today_str) {
        $_SESSION['last_daily_run'] = $today_str;
        include_once __DIR__ . '/../cron_daily.php';
    }
}
?>