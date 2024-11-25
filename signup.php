<?php
// Include database connection
include('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['smail'];
    $password = $_POST['spass'];

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert new user
    $query = "INSERT INTO users (email, password) VALUES ('$email', '$hashed_password')";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        echo "Account created successfully! Please wait for admin approval.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!-- Signup form -->
<form action="signup.php" method="POST">
    <label for="smail">Email</label>
    <input type="email" id="smail" name="smail" required>
    <label for="spass">Password</label>
    <input type="password" id="spass" name="spass" required>
    <input type="submit" value="Sign Up">
</form>
