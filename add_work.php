<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = trim($_POST['activity_name']);
    $activity_date = $_POST['activity_date'];
    $activity_time = $_POST['activity_time'];
    $user_id = $_SESSION['user_id'];

    $input_datetime = strtotime("$activity_date $activity_time");
    $current_datetime = time();

    if (!empty($activity_name) && !empty($activity_date) && !empty($activity_time)) {
        // Validation: පසුගිය දිනයක් හෝ වේලාවක්දැයි පරීක්ෂා කිරීම
        if ($input_datetime < $current_datetime) {
            $error = "You cannot schedule an activity for a past date or time!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO activities (user_id, activity_name, activity_date, activity_time) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $activity_name, $activity_date, $activity_time])) {
                $msg = "Activity added successfully!";
            } else {
                $error = "Failed to add activity.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Add New Activity</h2>
    <?php if($msg) echo "<div class='success-msg'>$msg</div>"; ?>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>

    <form action="add_work.php" method="POST" id="addWorkForm">
        <div class="form-group">
            <label>Activity Description</label>
            <input type="text" name="activity_name" placeholder="e.g. Team Meeting, Study PHP" required>
        </div>
        <div class="form-group">
            <label>Date</label>
            <!-- Set the minimum date to today -->
            <input type="date" name="activity_date" id="activity_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label>Time</label>
            <input type="time" name="activity_time" id="activity_time" required>
        </div>
        <button type="submit" class="btn">Add Activity</button>
    </form>
</div>

<script>
// Real-time validation for current day past time
document.getElementById('addWorkForm').addEventListener('submit', function(e) {
    const selectedDate = document.getElementById('activity_date').value;
    const selectedTime = document.getElementById('activity_time').value;
    const now = new Date();
    
    const todayStr = now.toISOString().split('T')[0];
    if (selectedDate === todayStr) {
        const currentTimeStr = now.toTimeString().split(' ')[0].substring(0, 5);
        if (selectedTime < currentTimeStr) {
            e.preventDefault();
            alert('Please select a future time for today!');
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>