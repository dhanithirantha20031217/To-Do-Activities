<?php
session_start();
require 'includes/db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify Password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        // Redirect to home page after successful login
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Login</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email (Username)</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p class="link-text">Don't have an account? <a href="signup.php">Sign up here</a></p>
</div>

<?php include 'includes/footer.php'; ?>