<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now_timestamp = time();

$stmt = $pdo->prepare("SELECT * FROM activities WHERE user_id = ? AND activity_date = ? ORDER BY activity_time ASC");
$stmt->execute([$user_id, $today]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! 👋</h2>
    <p class="subtitle">Here are your activities for today (<?php echo date('F j, Y'); ?>):</p>

    <div class="activity-list">
        <?php if (count($activities) > 0): ?>
            <?php foreach ($activities as $act): 
                $act_timestamp = strtotime($act['activity_date'] . ' ' . $act['activity_time']);
                
                // Status Determination
                if ($act['status'] == 'completed') {
                    $display_status = 'Completed';
                    $status_class = 'completed';
                } elseif ($act_timestamp < $now_timestamp) {
                    $display_status = 'Missed';
                    $status_class = 'missed';
                } else {
                    $display_status = 'Pending';
                    $status_class = 'pending';
                }
            ?>
                <div class="activity-card <?php echo $status_class; ?>">
                    <div class="activity-info">
                        <h3><?php echo htmlspecialchars($act['activity_name']); ?></h3>
                        <span class="time-badge">⏰ <?php echo date("g:i A", strtotime($act['activity_time'])); ?></span>
                    </div>
                    <span class="status-tag <?php echo $status_class; ?>">
                        <?php echo $display_status; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">No activities scheduled for today. Take a break or add a new task!</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>