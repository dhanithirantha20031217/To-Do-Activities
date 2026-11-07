<?php
require_once 'includes/db.php';
date_default_timezone_set('Asia/Colombo'); // ශ්‍රී ලංකා වේලාව සකස් කිරීම

require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d'); // අද දිනය (2026-11-07) ලබා ගනී

// අද දිනට අදාළ වැඩ පමණක් Database එකෙන් ලබා ගැනීම
$stmt = $pdo->prepare("SELECT * FROM activities WHERE user_id = ? AND activity_date = ? ORDER BY activity_time ASC");
$stmt->execute([$user_id, $today]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Home Page Card and UI Styling */
.home-wrapper {
    max-width: 650px;
    margin: 40px auto;
    padding: 0 15px;
}

.home-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 35px 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
}

.welcome-title {
    font-size: 26px;
    font-weight: 700;
    color: #2b2d42;
    margin-bottom: 5px;
}

.subtitle {
    font-size: 14px;
    color: #7d8597;
    margin-bottom: 25px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 5px solid #cbd5e1;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.activity-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.03);
}

/* Status colors */
.activity-item.status-completed {
    border-left-color: #2ecc71;
}

.activity-item.status-missed {
    border-left-color: #e74c3c;
}

.activity-item.status-pending {
    border-left-color: #3498db;
}

.act-details h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #2b2d42;
    font-weight: 600;
}

.time-badge {
    font-size: 13px;
    color: #8d99ae;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.badge.status-completed {
    background-color: #e8f8f0;
    color: #2ecc71;
}

.badge.status-missed {
    background-color: #fde8e8;
    color: #e74c3c;
}

.badge.status-pending {
    background-color: #eef6fc;
    color: #3498db;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #8d99ae;
}

.empty-state i {
    font-size: 45px;
    margin-bottom: 10px;
    color: #cbd5e1;
}
</style>

<div class="home-wrapper">
    <div class="home-card">
        <h2 class="welcome-title">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! 👋</h2>
        <p class="subtitle">Here are your activities for today (<?php echo date("F j, Y"); ?>):</p>

        <?php if (count($activities) > 0): ?>
            <div class="activity-list">
                <?php foreach ($activities as $act): ?>
                    <?php 
                        $act_time = strtotime($act['activity_date'] . ' ' . $act['activity_time']);
                        $is_past = ($act_time < time());

                        if ($act['status'] == 'completed') {
                            $statusClass = 'status-completed';
                            $statusText = 'Completed';
                        } elseif ($is_past) {
                            $statusClass = 'status-missed';
                            $statusText = 'Missed';
                        } else {
                            $statusClass = 'status-pending';
                            $statusText = 'Pending';
                        }
                    ?>
                    <div class="activity-item <?php echo $statusClass; ?>">
                        <div class="act-details">
                            <h4><?php echo htmlspecialchars($act['activity_name']); ?></h4>
                            <span class="time-badge">
                                <i class="far fa-clock"></i> <?php echo date("g:i A", strtotime($act['activity_time'])); ?>
                            </span>
                        </div>
                        <div class="act-status">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-tasks"></i>
                <p>No activities scheduled for today! 🎉</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>