<?php
include('db_config.php');

if (isset($_GET['user_id']) && isset($_GET['action'])) {
    $user_id = $_GET['user_id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $update_query = "UPDATE users SET status='approved' WHERE id='$user_id'";
    } else if ($action == 'reject') {
        $update_query = "UPDATE users SET status='rejected' WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "Account updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!-- <a href="admin.php?user_id=1&action=approve">Approve</a>
<a href="admin.php?user_id=1&action=reject">Reject</a> -->

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
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <td>Nick</td>
                            <td>nick@gmail.com</td>
                            <td>
                                <form action="">
                                    <a href="admin.php?user_id=1&action=approve">Approve</a>
                                    <a href="admin.php?user_id=1&action=reject">Reject</a>
                                    <!-- <input type="button" value="Accept" id="accept">
                                    <input type="button" value="Reject" id="reject"> -->
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>test</td>
                            <td>test@gmail.com</td>
                            <td>
                                <form action="">
                                    <input type="button" value="Accept" id="accept">
                                    <input type="button" value="Reject" id="reject">
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="admin-request col-100">
                    <h3>Approved Accounts</h3>
                    <table class="col-100">
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <td>Nick</td>
                            <td>nick@gmail.com</td>
                            <td>Approved</td>
                        </tr>
                        <tr>
                            <td>test</td>
                            <td>test@gmail.com</td>
                            <td>Rejected</td>
                        </tr>
                    </table>
                </div>

                <div class="admin-request col-100">
                    <h3>Projects Uploaded</h3>
                    <table class="col-100">
                        <tr>
                            <th>Project Name</th>
                            <th>Uploaded By</th>
                            <th>User Email</th>
                            <th>Department</th>
                        </tr>
                        <tr>
                            <td>Test Project</td>
                            <td>Testing Name</td>
                            <td>testing@gmail.com</td>
                            <td>Test123</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>