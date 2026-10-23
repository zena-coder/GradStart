<?php
require_once "auth.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$userId = $_SESSION['user_id'];

$opp = fetch_one($conn, "SELECT * FROM opportunity WHERE opportunity_id=?", "i", [$id]);
if (!$opp) {
    show_message_page("Opportunity not found", "This opportunity may have been removed.", 404, "Back to opportunities", "index.php");
}

$isSaved = fetch_one($conn, "SELECT bookmark_id FROM bookmark WHERE graduate_id=? AND opportunity_id=?", "ii", [$userId, $id]) ? true : false;

if (isset($_GET['save'])) {
    if ($isSaved) {
        execute($conn, "DELETE FROM bookmark WHERE graduate_id=? AND opportunity_id=?", "ii", [$userId, $id]);
        flash("Bookmark removed");
    } else {
        execute($conn, "INSERT INTO bookmark (graduate_id, opportunity_id) VALUES (?, ?)", "ii", [$userId, $id]);
        flash("Opportunity saved");
    }
    header("Location: details.php?id=" . $id);
    exit;
}

$skills = array_filter(array_map('trim', explode(",", $opp['skills_to_be_acquired'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo h($opp['title']); ?> - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <?php include "message.php"; ?>
        <div class="breadcrumb">
        <a href="index.php">Home</a><span>/</span>Opportunity Details
    </div>
    <div class="detail-box">
        <h2><?php echo h($opp['title']); ?></h2>
        <p class="meta">
            🏢 <?php echo h($opp['company']); ?><br>
            🎓 <?php echo h($opp['specialization']); ?><br>
            📍 <?php echo h($opp['location']); ?> &nbsp; ⏱ <?php echo h($opp['duration']); ?>
        </p>

        <h3>Required Skills</h3>
        <div>
            <?php foreach ($skills as $skill) { ?>
                <span class="tag"><?php echo h($skill); ?></span>
            <?php } ?>
        </div>

        <h3>Description</h3>
        <p><?php echo nl2br(h($opp['description'])); ?></p>

        <p><strong>Application Deadline:</strong> <?php echo h(date("F j, Y", strtotime($opp['deadline']))); ?></p>

        <div class="card-actions">
            <a href="<?php echo h(apply_url($opp['application_link'])); ?>" target="_blank" rel="noopener" class="btn btn-primary">Apply Now</a>
            <a href="details.php?id=<?php echo $id; ?>&save=1" class="btn btn-outline"><?php echo bookmark_icon($isSaved); ?> <?php echo $isSaved ? "Bookmarked" : "Bookmark"; ?></a>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
</body>
</html>
