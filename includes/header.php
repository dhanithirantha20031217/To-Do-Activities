<?php
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