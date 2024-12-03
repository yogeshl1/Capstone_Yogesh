<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // Accept or Reject

    $status = ($action == 'Accept') ? 'Active' : 'Rejected';

    $sql = "UPDATE users SET status = '$status' WHERE id = $user_id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User status updated successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM users WHERE status = 'Pending'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin col-100">
        <div class="admin-main col-100">
            <div class="admin-greeting col-100">
                <h2>Hello Admin👋</h2>
            </div>
            <div class="admin-table col-100">
                <div class="admin-request col-100">
                    <h3>New Accounts</h3>
                    <table class="col-100">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" id="accept" value="Accept">Accept</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" id="reject" value="Reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
