<?php
require_once "auth.php";

$userId = $_SESSION['user_id'];
$q = isset($_GET['q']) ? trim($_GET['q']) : "";
$selected = [];
if (isset($_GET['skills']) && is_array($_GET['skills'])) {
    foreach ($_GET['skills'] as $skill) {
        if (is_string($skill) && trim($skill) !== "") {
            $selected[] = trim($skill);
        }
    }
}
$queryString = http_build_query(['q' => $q, 'skills' => $selected]);

$savedRows = fetch_all($conn, "SELECT opportunity_id FROM bookmark WHERE graduate_id=?", "i", [$userId]);
$savedIds = array_map('intval', array_column($savedRows, 'opportunity_id'));

if (isset($_GET['save'])) {
    $oppId = intval($_GET['save']);
    if (in_array($oppId, $savedIds)) {
        execute($conn, "DELETE FROM bookmark WHERE graduate_id=? AND opportunity_id=?", "ii", [$userId, $oppId]);
        flash("Bookmark removed");
    } elseif (fetch_one($conn, "SELECT opportunity_id FROM opportunity WHERE opportunity_id=?", "i", [$oppId])) {
        execute($conn, "INSERT INTO bookmark (graduate_id, opportunity_id) VALUES (?, ?)", "ii", [$userId, $oppId]);
        flash("Opportunity saved");
    }
    header("Location: index.php?" . $queryString);
    exit;
}

$all = fetch_all($conn, "SELECT * FROM opportunity ORDER BY opportunity_id DESC");
$totalCount = count($all);

$skillMap = [];
foreach ($all as $row) {
    foreach (explode(",", $row['skills_to_be_acquired']) as $skill) {
        $skill = trim($skill);
        $key = mb_strtolower($skill);
        if ($skill !== "" && !isset($skillMap[$key])) {
            $skillMap[$key] = $skill;
        }
    }
}
$allSkills = array_values($skillMap);
sort($allSkills, SORT_FLAG_CASE | SORT_STRING);

$selectedLower = array_map('mb_strtolower', $selected);

$results = array_filter($all, function ($row) use ($q, $selectedLower) {
    if ($q !== "") {
        $found = false;
        foreach (['title', 'company','specialization', 'description'] as $field) {
            if (mb_stripos($row[$field], $q) !== false) {
                $found = true;
            }
        }
        if (!$found) {
            return false;
        }
    }
    if (count($selectedLower) > 0) {
        $rowSkills = array_map(function ($s) {
            return mb_strtolower(trim($s));
        }, explode(",", $row['skills_to_be_acquired']));
        if (count(array_intersect($selectedLower, $rowSkills)) == 0) {
            return false;
        }
    }
    return true;
});

$count = count($results);
$isFiltered = ($q !== "" || count($selected) > 0);
$firstName = explode(" ", trim($_SESSION['full_name']))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Training Opportunities - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <?php include "message.php"; ?>
    <div class="hero">
        <h1>Hello, <?php echo h($firstName); ?>! 👋</h1>
        <p>Ready to find the training that launches your career? Explore the opportunities below.</p>
    </div>

    <form method="GET" action="index.php">
        <div class="search-bar">
            <input type="text" name="q" placeholder="Search by title, company, specialization, or description..." value="<?php echo h($q); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($isFiltered) { ?>
                <a href="index.php" class="btn btn-outline">Clear filters</a>
            <?php } ?>
        </div>
        <?php if (count($allSkills) > 0) { ?>
        <div class="skills-filter">
            <strong>Filter by skills:</strong>
            <?php foreach ($allSkills as $skill) { ?>
                <label class="skill-option">
                    <input type="checkbox" name="skills[]" value="<?php echo h($skill); ?>" <?php if (in_array(mb_strtolower($skill), $selectedLower)) echo "checked"; ?>>
                    <?php echo h($skill); ?>
                </label>
            <?php } ?>
        </div>
        <?php } ?>
    </form>

    <?php if ($count == 0) { ?>
        <div class="empty-state">
            <?php
            if ($totalCount == 0) {
                echo "No opportunities are currently available";
            } elseif ($q !== "") {
                echo "No results found";
            } else {
                echo "No matching opportunities found";
            }
            ?>
        </div>
    <?php } else { ?>
        <div class="grid">
            <?php foreach ($results as $opp) {
                $isSaved = in_array(intval($opp['opportunity_id']), $savedIds); ?>
                <div class="card">
                    <a href="index.php?save=<?php echo $opp['opportunity_id']; ?>&<?php echo h($queryString); ?>" class="save-link <?php if ($isSaved) echo 'saved'; ?>" title="<?php echo $isSaved ? 'Remove bookmark' : 'Bookmark'; ?>"><?php echo bookmark_icon($isSaved); ?></a>
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
