<?php
session_start(); // Start the session
include('config.php'); // Database connection

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Hardcoded admin credentials
$admin_email = "admin@gmail.com";
$admin_password = "admin123";

$error_message = ""; // Initialize error message

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['pass'];

        if (empty($email) || empty($password)) {
            throw new Exception("Email and Password are required fields.");
        }

        // Check if the user is the admin
        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION['is_admin'] = true; // Set admin session
            header("Location: admin.php"); // Redirect to admin dashboard
            exit();
        }
        
        
        // Secure query to check user existence
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password_hash'])) {
                if ($user['status'] === 'Active') {
                    // Store session variables
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'];

                    // Redirect to profile.php
                    header("Location: profile.php");
                    exit();
                } elseif ($user['status'] === 'Pending') {
                    $error_message = "Your account is pending approval.";
                } elseif ($user['status'] === 'Rejected') {
                    $error_message = "Your account has been rejected.";
                }
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No account found with this email.";
        }

        $stmt->close();
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

$conn->close();
?>
<!-- Rest of the HTML form -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="signup-container col-100">
        <div class="signup-main col-100 common align">
            <div class="signup-form col-30">
                <form action="signin.php" method="POST" class="col-100 common flex-col">
                    <h3>Sign In</h3>
                    <?php if (!empty($error_message)): ?>
                        <p style="color: red;text-align: center;padding: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>
                    <label for="mail">Email</label>
                    <input type="email" id="smail" name="mail" required>
                    <label for="pass">Password</label>
                    <input type="password" id="spass" name="pass" required>
                    <input type="submit" value="Sign In">
                    <p class="common">Don't have an account? <a href="signup.php">Sign up</a></p>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
