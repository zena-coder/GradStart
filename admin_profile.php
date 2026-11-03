<?php
require_once "admin_auth.php";
$user = fetch_one($conn, "SELECT name, email FROM user WHERE user_id=?", "i", [$_SESSION['user_id']]);
if (!$user) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Profile - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "admin_navbar.php"; ?>
<div class="container">
    <?php include "message.php"; ?>
	    <div class="breadcrumb">
        <a href="admin_dashboard.php">Dashboard</a><span>/</span>Profile
    </div>
    <div class="profile-box">
        <div class="profile-avatar"><?php echo h(mb_strtoupper(mb_substr($user['name'], 0, 1))); ?></div>
        <h2>My Profile</h2>
        <div class="profile-row">
            <span>Name</span>
            <strong><?php echo h($user['name']); ?></strong>
        </div>
        <div class="profile-row">
            <span>Email</span>
            <strong><?php echo h($user['email']); ?></strong>
        </div>
        <a href="admin_change_password.php" class="btn btn-primary btn-block">Change Password</a>
    </div>
</div>
</body>
</html>
