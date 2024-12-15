<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: signin.php");
    exit();
}

include('config.php'); // Include the database connection

// Handle project creation
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['project_name'];
    $description = $_POST['project_desc'];
    $project_link = $_POST['project_link'];

    // Get logged-in user's details
    $author_first_name = $_SESSION['user_name'];
    $author_email = $_SESSION['user_email'];

    if (empty($title) || empty($description) || empty($project_link)) {
        $error_message = "All fields are required.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO projects (title, description, project_link, author_first_name, author_email) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare the statement: " . $conn->error);
            }
            $stmt->bind_param("sssss", $title, $description, $project_link, $author_first_name, $author_email);

            if ($stmt->execute()) {
                header("Location: department.php");
                exit();
            } else {
                throw new Exception("Failed to execute the statement: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch existing projects
$projects = [];
try {
    $result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    }
} catch (Exception $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department | Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Section -->
    <div class="home-container col-100">
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
    </div>

    <!-- Add Project Form -->
    <div class="departments-main common-around flex-col">
        <h2 class="common">Department Projects</h2>

        <div class="signup-form col-40">
            <h3>Add a New Project</h3>
            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="department.php" method="POST" class="col-100 common flex-col">
                <label for="project-name">Project Title</label>
                <input type="text" id="project-name" name="project_name" required>

                <label for="project-desc">Project Description</label>
                <textarea id="project-desc" name="project_desc" rows="4" required></textarea>

                <label for="project-link">Project Link</label>
                <input type="url" id="project-link" name="project_link" required>

                <input type="submit" value="Add Project">
            </form>
        </div>

        <!-- Project List -->
        <div class="department-main common-around flex-row">
            <?php foreach ($projects as $project): ?>
                <div class="dept-box col-30">
                    <h5><span>Title:</span><?php echo htmlspecialchars($project['title']); ?></h5>
                    <p><span>Project Description:</span><?php echo htmlspecialchars($project['description']); ?></p>
                    <p>Author: <?php echo htmlspecialchars($project['author_first_name']); ?> (<?php echo htmlspecialchars($project['author_email']); ?>)</p>
                    <a href="<?php echo htmlspecialchars($project['project_link']); ?>" target="_blank">View Project</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
