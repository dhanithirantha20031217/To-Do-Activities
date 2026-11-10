<?php

date_default_timezone_set('Asia/Colombo'); 

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

$pdo->exec("SET time_zone = '+05:30'");

// Auto-delete activities older than 30 days
$cutoff_date = date('Y-m-d', strtotime('-30 days'));
$delete_stmt = $pdo->prepare("DELETE FROM activities WHERE activity_date < ?");
$delete_stmt->execute([$cutoff_date]);

$today = date('Y-m-d'); 

// Fetch all registered users
$user_stmt = $pdo->query("SELECT id, first_name, email FROM users");
$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $user_id = $user['id'];
    $user_email = $user['email'];
    $user_name = $user['first_name'];

    // Fetch activities for today
    $act_stmt = $pdo->prepare("SELECT activity_name, activity_time, status FROM activities WHERE user_id = ? AND activity_date = ? ORDER BY activity_time ASC");
    $act_stmt->execute([$user_id, $today]);
    $activities = $act_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($activities) > 0) {
        // CASE 1: User HAS activities today
        $subject = "To-Do Activities: Your Schedule for Today ($today)";
        
        $bodyHTML = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f6f9; border-radius: 10px;'>
            <h2 style='color: #333;'>Good Morning, {$user_name}! ☀️</h2>
            <p style='color: #555;'>Here is your activity schedule for today (<b>{$today}</b>):</p>
            <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                <thead>
                    <tr style='background-color: #a6c1ee; color: white;'>
                        <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Time</th>
                        <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Activity</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($activities as $act) {
            $formatted_time = date("g:i A", strtotime($act['activity_time']));
            $bodyHTML .= "
                    <tr style='background-color: #ffffff;'>
                        <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>{$formatted_time}</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$act['activity_name']}</td>
                    </tr>";
        }

        $bodyHTML .= "
                </tbody>
            </table>
            <p style='margin-top: 20px; color: #777; font-size: 13px;'>Have a productive day!<br><b>To-Do Activities Team</b></p>
        </div>";

        sendActivityEmail($user_email, $user_name, $subject, $bodyHTML);
    } else {
        // CASE 2: User HAS NO activities today (Send friendly notification)
        $subject = "To-Do Activities: No Tasks Scheduled for Today ($today)";
        
        $bodyHTML = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #e8f8f0; border-radius: 10px; border-left: 5px solid #2ecc71;'>
            <h2 style='color: #2b2d42;'>Good Morning, {$user_name}! ☀️</h2>
            <p style='color: #555;'>You have <b>no activities scheduled</b> for today (<b>{$today}</b>).</p>
            <p style='color: #555;'>Enjoy your free time, or log in to schedule new tasks for today!</p>
            <p style='margin-top: 20px; color: #777; font-size: 13px;'>Have a relaxed and wonderful day!<br><b>To-Do Activities Team</b></p>
        </div>";

        sendActivityEmail($user_email, $user_name, $subject, $bodyHTML);
    }
}

?>