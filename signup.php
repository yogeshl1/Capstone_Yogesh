<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $first_name = mysqli_real_escape_string($conn, $_POST['fname']);
    $email = mysqli_real_escape_string($conn, $_POST['mail']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database with 'Pending' status
    $sql = "INSERT INTO users (first_name, email, password_hash, status) VALUES ('$first_name', '$email', '$password_hash', 'Pending')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Redirect with success message
        echo "<script>alert('Sign-up successful! Please wait for admin approval.'); window.location.href = 'signup.php';</script>";
    } else {
        // Display the error message if something goes wrong
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href = 'signup.php';</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="signup-container col-100">
        <div class="signup-main col-100 common align">
            <div class="signup-form col-30">
                <form action="signup.php" method="POST" class="col-100 common flex-col">
                    <h3>Sign up</h3>
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname" required>
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" required>
                    <label for="pass">Password</label>
                    <input type="password" id="pass" name="pass" required>
                    <span class="common">
                        <input type="checkbox" required>
                        <p>By creating account you agree to share your information with the admin.</p>
                    </span>
                    <input type="submit" value="Sign Up">
                    <p class="common">Already have an account? <a href="signin.php">Sign in</a></p>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
