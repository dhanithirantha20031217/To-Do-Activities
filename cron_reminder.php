<?php
// reminder.php - cron job to send email reminders for activities starting within the next 10 minutes
date_default_timezone_set('Asia/Colombo'); 

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

$today = date('Y-m-d');
$current_time = time();


$query = "
    SELECT a.id, a.activity_name, a.activity_time, a.activity_date, u.email, u.first_name 
    FROM activities a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.activity_date = ? 
      AND a.reminder_sent = 0 
      AND a.status = 'pending'
";

$stmt = $pdo->prepare("SET time_zone = '+05:30'");
$stmt->execute();

$stmt = $pdo->prepare($query);
$stmt->execute([$today]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($activities as $act) {
    $activity_timestamp = strtotime($act['activity_date'] . ' ' . $act['activity_time']);
    $minutes_until_start = ($activity_timestamp - $current_time) / 60;

    // Check if the activity is starting within the next 10 minutes
    if ($minutes_until_start >= 0 && $minutes_until_start <= 10) {
        $formatted_time = date("g:i A", strtotime($act['activity_time']));
        $subject = "⏰ Reminder: '{$act['activity_name']}' starts soon!";
        
        $bodyHTML = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #fff3cd; border-left: 5px solid #ffc107; border-radius: 8px;'>
            <h2 style='color: #856404;'>Upcoming Activity Reminder ⏰</h2>
            <p style='color: #333;'>Hi <b>{$act['first_name']}</b>,</p>
            <p style='color: #333;'>Your activity <b>'{$act['activity_name']}'</b> is scheduled for <b>{$formatted_time}</b>.</p>
            <p style='margin-top: 20px; color: #777; font-size: 13px;'>Get ready!<br><b>To-Do Activities Team</b></p>
        </div>";

        if (sendActivityEmail($act['email'], $act['first_name'], $subject, $bodyHTML)) {
            // Mark the reminder as sent to avoid duplicate emails
            $update_stmt = $pdo->prepare("UPDATE activities SET reminder_sent = 1 WHERE id = ?");
            $update_stmt->execute([$act['id']]);
        }
    }
}

echo "Reminder check script executed successfully.";
?>