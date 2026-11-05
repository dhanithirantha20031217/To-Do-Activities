<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$activities = [];
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$now_timestamp = time();

// Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $del_stmt = $pdo->prepare("DELETE FROM activities WHERE id = ? AND user_id = ?");
    $del_stmt->execute([$delete_id, $user_id]);
    header("Location: show_work.php?start_date=$start_date&end_date=$end_date");
    exit();
}

// Toggle Complete Action
if (isset($_GET['toggle_id'])) {
    $toggle_id = $_GET['toggle_id'];
    $curr_status = $_GET['status'] == 'completed' ? 'pending' : 'completed';
    $tog_stmt = $pdo->prepare("UPDATE activities SET status = ? WHERE id = ? AND user_id = ?");
    $tog_stmt->execute([$curr_status, $toggle_id, $user_id]);
    header("Location: show_work.php?start_date=$start_date&end_date=$end_date");
    exit();
}

if (!empty($start_date) && !empty($end_date)) {
    $stmt = $pdo->prepare("SELECT * FROM activities WHERE user_id = ? AND activity_date BETWEEN ? AND ? ORDER BY activity_date ASC, activity_time ASC");
    $stmt->execute([$user_id, $start_date, $end_date]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Filter & Search Activities</h2>
    
    <form action="show_work.php" method="GET" class="filter-form">
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
        </div>
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
        </div>
        <button type="submit" class="btn search-btn">Filter Activities</button>
    </form>

    <div class="activity-list">
        <?php if (!empty($start_date) && !empty($end_date)): ?>
            <h3>Results from <?php echo $start_date; ?> to <?php echo $end_date; ?></h3>
            <?php if (count($activities) > 0): ?>
                <?php foreach ($activities as $act): 
                    $act_timestamp = strtotime($act['activity_date'] . ' ' . $act['activity_time']);
                    
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
                            <p>📅 <?php echo $act['activity_date']; ?> | ⏰ <?php echo date("g:i A", strtotime($act['activity_time'])); ?></p>
                        </div>
                        <div class="action-buttons">
                            <a href="show_work.php?toggle_id=<?php echo $act['id']; ?>&status=<?php echo $act['status']; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                               class="status-btn <?php echo $status_class; ?>">
                               <?php echo $act['status'] == 'completed' ? '✓ Done' : 'Mark Done'; ?>
                            </a>
                            <a href="show_work.php?delete_id=<?php echo $act['id']; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                               class="delete-btn" onclick="return confirm('Are you sure you want to delete this activity?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No activities found in this date range.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="no-data">Select a Start Date and End Date above to view activities.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>