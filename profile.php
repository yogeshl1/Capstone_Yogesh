<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: signin.php");
    exit();
}

include('config.php'); // Include database connection

$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];

// Fetch projects uploaded by the logged-in user
$projects = [];
try {
    $stmt = $conn->prepare("SELECT p.id, p.title, p.description, d.name AS department_name 
                            FROM projects p 
                            INNER JOIN departments d ON p.department_id = d.id 
                            WHERE p.author_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    die("Error fetching projects: " . $e->getMessage());
}

// Handle project deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND author_email = ?");
        $stmt->bind_param("is", $project_id, $user_email);
        if ($stmt->execute()) {
            header("Location: profile.php"); // Refresh the page after deletion
            exit();
        }
        $stmt->close();
    } catch (Exception $e) {
        die("Error deleting project: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="home-container col-100">
        <div class="main col-100 common align flex-col">
            <div class="navigation col-100">
                <div class="navigation-main col-100 common">
                    <div class="nav-logo col-20 common align">
                        <a href=""><img src="assets/images/logo.png" alt="logo"></a>
                    </div>
                    <div class="nav-links common-even align col-80">
                        <a href="departments.php">Departments</a>
                        <a href="profile.php" id="navLinks">My Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
            <div class="profile-main common flex-col col-100">
                <div class="profile col-100 common">
                    <div class="profile-img common align col-40">
                        <img src="assets/images/profile.png" alt="profile" width="200px">
                    </div>
                    <div class="profile-det common flex-col col-60">
                        <h3>Full Name</h3>
                        <p><?php echo htmlspecialchars($user_name); ?></p><br>
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>
                <div class="myprojects common col-100 flex-col align" style="justify-content: flex-start;">
                    <h3>My Projects</h3><br>
                    <!-- Projects List -->
                <div class="projects-list common-even col-100">
                    <?php if (empty($projects)): ?>
                        <p>No projects found.</p>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="list-box">
                                <span class="common"><b>Title: </b><p><?php echo htmlspecialchars($project['title']); ?></p></span>
                                <span class="common"><b>Description: </b><p><?php echo htmlspecialchars($project['description']); ?></p></span>
                                <span class="common"><b>Department: </b><p><?php echo htmlspecialchars($project['department_name']); ?></p></span>
                                <form action="profile.php" method="POST">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <input type="submit" name="delete_project" value="Delete">
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
