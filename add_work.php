<?php
require_once 'includes/db.php';
date_default_timezone_set('Asia/Colombo'); 

$error = '';
$success = '';

require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id       = $_SESSION['user_id'];
    $activity_name = trim($_POST['activity_name']);
    $activity_date = $_POST['activity_date'];
    $activity_time = $_POST['activity_time'];
    $created_at    = date('Y-m-d H:i:s'); 

    // Future date/time validation
    $input_datetime = strtotime("$activity_date $activity_time");
    $current_datetime = time();

    if ($input_datetime < $current_datetime) {
        $error = "You cannot add an activity for a past date or time!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO activities (user_id, activity_name, activity_date, activity_time, status, reminder_sent, created_at) VALUES (?, ?, ?, ?, 'pending', 0, ?)");
        if ($stmt->execute([$user_id, $activity_name, $activity_date, $activity_time, $created_at])) {
            $success = "Activity added successfully!";
        } else {
            $error = "Failed to add activity. Please try again.";
        }
    }
}
?>

<div class="form-container">
    <h2>Add New Activity</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <?php if($success) echo "<div class='success-msg'>$success</div>"; ?>
    
    <form action="add_work.php" method="POST">
        <div class="form-group">
            <label>Activity Description</label>
            <input type="text" name="activity_name" placeholder="e.g. Team Meeting, Study PHP" required>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="activity_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label>Time</label>
            <input type="time" name="activity_time" required>
        </div>
        <button type="submit" class="btn">Add Activity</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>