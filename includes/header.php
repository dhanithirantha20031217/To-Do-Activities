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
    <!-- Dynamic version tag to prevent browser CSS caching -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="logo-container">
        <img src="images/logo.png" alt="Logo" class="round-logo" onerror="this.src='https://via.placeholder.com/50'">
        <h1>To-Do Activities</h1>
    </div>

    <!-- Navigation bar displayed when user is logged in -->
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
// Pseudo-Cron Trigger ONLY for 5-Minute Reminders
// Daily schedule email is now strictly handled by cron-job.org at 12:00 AM
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['last_reminder_run']) || (time() - $_SESSION['last_reminder_run']) > 300) {
        $_SESSION['last_reminder_run'] = time();
        include_once __DIR__ . '/../cron_reminder.php';
    }
}
?>