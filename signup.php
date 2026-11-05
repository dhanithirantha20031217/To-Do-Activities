<?php
require 'includes/db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password validation (8 chars, letter, number, symbol)
    $pattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    
    if (preg_match($pattern, $password)) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered. Please Login.";
        } else {
            // Insert user into database
            $insert = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            if($insert->execute([$first_name, $last_name, $email, $hashed_password])) {
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