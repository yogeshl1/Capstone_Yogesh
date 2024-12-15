<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: signin.php");
    exit();
}

include('config.php'); // Include database connection

// Fetch departments from the database
$departments = [];
try {
    $result = $conn->query("SELECT * FROM departments ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    } else {
        throw new Exception("Error fetching departments: " . $conn->error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="home-container col-100">
        <div class="main col-100 common align flex-col">
            <!-- Navigation -->
            <div class="navigation col-100">
                <div class="navigation-main col-100 common">
                    <div class="nav-logo col-20 common align">
                        <a href=""><img src="assets/images/logo.png" alt="logo"></a>
                    </div>
                    <div class="nav-links common-even align col-80">
                        <a href="departments.php" id="navLinks">Departments</a>
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Departments Section -->
            <div class="departments common col-100">
                <div class="departments-main common-bet col-90">
                    <?php foreach ($departments as $department): ?>
                        <div class="dept-box common flex-col align" data-aos="fade-down" data-aos-duration="1000">
                            <img src="assets/images/image-<?php echo htmlspecialchars($department['id']); ?>.jpg" alt="image">
                            <h5><?php echo htmlspecialchars($department['name']); ?></h5>
                            <a href="projects.php?department_id=<?php echo $department['id']; ?>">Explore</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
