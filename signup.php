<?php
require_once 'includes/db.php';
date_default_timezone_set('Asia/Colombo'); // ශ්‍රී ලංකා වේලාව සකස් කිරීම

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];

    // Password validation (8 chars, letter, number, symbol)
    $pattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (preg_match($pattern, $password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered. Please Login.";
        } else {
            // ශ්‍රී ලංකා වේලාවට අනුව (Asia/Colombo) නිර්මාණය වන වෙලාව ලබා ගැනීම
            $created_at = date('Y-m-d H:i:s');

            // Insert user into database with Sri Lanka timestamp
            $insert = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, created_at) VALUES (?, ?, ?, ?, ?)");
            
            if ($insert->execute([$first_name, $last_name, $email, $hashed_password, $created_at])) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong!";
            }
        }
    } else {
        $error = "Password must be at least 8 characters long and contain a letter, a number, and a symbol.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Sign Up</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <form action="signup.php" method="POST">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="Enter your first name" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Enter your last name" required>
        </div>
        <div class="form-group">
            <label>Email (Username)</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn">Sign Up</button>
    </form>
    <p class="link-text">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include 'includes/footer.php'; ?>