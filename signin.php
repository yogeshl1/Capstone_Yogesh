<?php
// Include database connection
include('db_config.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['smail'];
    $password = $_POST['spass'];

    // SQL query to get the user based on email
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Check if user exists and password matches
    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Check if account is approved
            if ($user['status'] == 'approved') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header('Location: dashboard.php'); // Redirect to dashboard
            } else if ($user['status'] == 'rejected') {
                echo "Your account has been rejected by the admin.";
            } else {
                echo "Your account is still under verification.";
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>

<!-- Signin form -->
<form action="signin.php" method="POST">
    <label for="smail">Email</label>
    <input type="email" id="smail" name="smail" required>
    <label for="spass">Password</label>
    <input type="password" id="spass" name="spass" required>
    <input type="submit" value="Sign In">
</form>
