<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: signin.php");
    exit();
}

include('config.php');

// Initialize variables
$error_message = "";
$success_message = "";
$department_id = $_GET['department_id'] ?? null;

// Validate department_id
if (!$department_id) {
    die("Invalid department selected.");
}

$department_name = "Unknown Department"; // Default fallback
if (isset($_GET['department_id'])) {
    $department_id = intval($_GET['department_id']); // Sanitize input
    $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $stmt->bind_result($fetched_department_name);
    if ($stmt->fetch()) {
        $department_name = $fetched_department_name;
    }
    $stmt->close();
}

// Handle project creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $title = $_POST['project_name'];
    $description = $_POST['project_desc'];
    $project_link = $_POST['project_link'];
    $author_first_name = $_SESSION['user_name'];
    $author_email = $_SESSION['user_email'];

    if (empty($title) || empty($description) || empty($project_link)) {
        $error_message = "All fields are required.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO projects (title, description, project_link, author_first_name, author_email, department_id) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare the statement: " . $conn->error);
            }
            $stmt->bind_param("sssssi", $title, $description, $project_link, $author_first_name, $author_email, $department_id);
            if ($stmt->execute()) {
                $success_message = "Project added successfully.";
            } else {
                throw new Exception("Failed to execute the statement: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $project_id = $_POST['project_id'];
    $user_email = $_SESSION['user_email'];
    $comment = $_POST['comment'];

    if (empty($comment)) {
        $error_message = "Comment cannot be empty.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (project_id, user_email, comment) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare the statement: " . $conn->error);
            }
            $stmt->bind_param("iss", $project_id, $user_email, $comment);
            if ($stmt->execute()) {
                $success_message = "Comment added successfully.";
            } else {
                throw new Exception("Failed to execute the statement: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch projects for the selected department
$projects = [];
try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE department_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
} catch (Exception $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
}

// Fetch comments for each project
$comments = [];
try {
    $result = $conn->query("SELECT * FROM comments");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $comments[$row['project_id']][] = $row;
        }
    }
} catch (Exception $e) {
    $error_message = "Error fetching comments: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Projects</title>
    <script src="https://kit.fontawesome.com/e3831a00ca.js" crossorigin="anonymous"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                        <a href="departments.php" id="navLinks">Departments</a>
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        <div class="projects col-100 common flex-col">
            <div class="back">
                    <a href="departments.php"><i class="fa fa-arrow-left"></i> Back</a>
                </div>
            <div class="projects-title col-100 common">
                <h2><?php echo htmlspecialchars($department_name); ?></h2>
            </div>

            <div class="projects-main common-around flex-row" data-aos="fade-right" data-aos-duration="1000">
                <?php foreach ($projects as $project): ?>
                    <div class="projectBox">
                        <span class="col-100 common"><b>Title:</b><p> <?php echo htmlspecialchars($project['title']); ?></p></span>
                        <span class="col-100"><b>Description:</b><p style="margin: 0;"> <?php echo htmlspecialchars($project['description']); ?></p></span>
                        <span class="col-100 common"><b>Author:</b><p><?php echo htmlspecialchars($project['author_first_name']); ?> (<?php echo htmlspecialchars($project['author_email']); ?>)</p></span>
                        <a href="<?php echo htmlspecialchars($project['project_link']); ?>" target="_blank">View Project</a>
                        <div class="comments">
                            <b>Comments</b>
                            <?php if (isset($comments[$project['id']])): ?>
                                <?php foreach ($comments[$project['id']] as $comment): ?>
                                    <p><?php echo htmlspecialchars($comment['comment']); ?> - <em style="color:red;"><?php echo htmlspecialchars($comment['user_email']); ?></em></p>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No comments yet.</p>
                            <?php endif; ?>
                        </div>
                        <div class="add-comment col-100">
                            <form action="projects.php?department_id=<?php echo htmlspecialchars($department_id); ?>" method="POST" class="common align flex-col col-100">
                                <input type="hidden" name="add_comment" value="1">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <textarea name="comment" id="comment" rows="2" placeholder="Add a comment" required></textarea>
                                <input type="submit" value="Comment">
                            </form>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="add-project col-100" id="addProject">
            <div class="add-main col-100">
                <h3>Add a New Project</h3>
                <?php if (!empty($error_message)): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
                <form action="projects.php?department_id=<?php echo htmlspecialchars($department_id); ?>" method="POST" class="col-100 common flex-col">
                    <input type="hidden" name="add_project" value="1">
                    <label for="project-name">Project Title</label>
                    <input type="text" id="project-name" name="project_name" required>
                    <label for="project-desc">Project Description</label>
                    <textarea id="project-desc" name="project_desc" rows="4" required></textarea>
                    <label for="project-link">Project Link</label>
                    <input type="url" id="project-link" name="project_link" required>
                    <input type="submit" value="Add Project">
                </form>
            </div>
        </div>
        <div class="add-button">
            <p onclick="addProject()"><i class="fa fa-plus"></i> Add New Project</p>
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
