<?php
require_once "auth.php";

$userId = $_SESSION['user_id'];

if (isset($_GET['remove'])) {
    $oppId = intval($_GET['remove']);
    execute($conn, "DELETE FROM bookmark WHERE graduate_id=? AND opportunity_id=?", "ii", [$userId, $oppId]);
    flash("Bookmark removed");
    header("Location: saved.php");
    exit;
}

$results = fetch_all($conn, "SELECT o.* FROM opportunity o JOIN bookmark b ON o.opportunity_id = b.opportunity_id WHERE b.graduate_id=? ORDER BY b.bookmark_id DESC", "i", [$userId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Saved Opportunities - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <?php include "message.php"; ?>
    <h2>My Saved Opportunities</h2>
    <?php if (count($results) == 0) { ?>
        <div class="empty-state">
            No saved opportunities yet<br><br>
            <a href="index.php" class="btn btn-primary">Browse Opportunities</a>
        </div>
    <?php } else { ?>
        <div class="grid">
            <?php foreach ($results as $opp) { ?>
                <div class="card">
                    <a href="saved.php?remove=<?php echo $opp['opportunity_id']; ?>" class="save-link saved" title="Remove bookmark"><?php echo bookmark_icon(true); ?></a>
                    <h3><?php echo h($opp['title']); ?></h3>
                    <p>🏢 <?php echo h($opp['company']); ?></p>
                    <p>🎓 <?php echo h($opp['specialization']); ?></p>
                    <p>📍 <?php echo h($opp['location']); ?></p>
                    <p>⏱ <?php echo h($opp['duration']); ?></p>
                    <div class="card-actions">
                        <a href="details.php?id=<?php echo $opp['opportunity_id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php include "footer.php"; ?>
</body>
</html>
