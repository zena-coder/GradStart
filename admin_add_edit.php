<?php
require_once "admin_auth.php";

$fields = ['title', 'company', 'specialization', 'description', 'location', 'duration', 'skills', 'deadline', 'apply_link'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";
$errorFields = [];
$opp = [];
foreach ($fields as $field) {
    $opp[$field] = "";
}
$original = null;

if ($id > 0) {
    $original = fetch_one($conn, "SELECT * FROM opportunity WHERE opportunity_id=?", "i", [$id]);
    if (!$original) {
        show_message_page("Opportunity not found", "This opportunity may have been removed.", 404, "Back to dashboard", "admin_dashboard.php");
    }
    foreach ($fields as $field) {
        $opp[$field] = $original[$field];
    }
    $opp['skills'] = $original['skills_to_be_acquired'];
    $opp['apply_link'] = $original['application_link'];
    $original['apply_link'] = $original['application_link'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($fields as $field) {
        $opp[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : "";
        if ($opp[$field] === "") {
            $errorFields[] = $field;
        }
    }

    if (count($errorFields) > 0) {
        $error = "Please fill in all required fields";
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $opp['deadline']);
        $validDate = $date && $date->format('Y-m-d') === $opp['deadline'];
        $deadlineChanged = ($id == 0 || $opp['deadline'] !== $original['deadline']);

        $link = $opp['apply_link'];
        if (!filter_var($link, FILTER_VALIDATE_EMAIL)) {
            if (!preg_match('#^https?://#i', $link)) {
                $link = "https://" . $link;
            }
        }
        $validLink = filter_var($link, FILTER_VALIDATE_EMAIL) || (filter_var($link, FILTER_VALIDATE_URL) && strpos(parse_url($link, PHP_URL_HOST), ".") !== false);

        if (!$validDate) {
            $error = "Please enter a valid deadline date";
            $errorFields = ["deadline"];
        } elseif ($deadlineChanged && $opp['deadline'] < date('Y-m-d')) {
            $error = "Please enter a valid deadline date. It can't be in the past.";
            $errorFields = ["deadline"];
        } elseif (!$validLink) {
            $error = "Please enter a valid application link or email";
            $errorFields = ["apply_link"];
        } else {
            $skillList = array_filter(array_map('trim', explode(",", $opp['skills'])));
            $skills = implode(", ", $skillList);

            if ($id > 0) {
                execute($conn, "UPDATE opportunity SET title=?, company=?, specialization=?, description=?, location=?, duration=?, skills_to_be_acquired=?, deadline=?, application_link=? WHERE opportunity_id=?", "sssssssssi", [$opp['title'], $opp['company'], $opp['specialization'], $opp['description'], $opp['location'], $opp['duration'], $skills, $opp['deadline'], $link, $id]);
                flash("Opportunity updated successfully");
            } else {
                execute($conn, "INSERT INTO opportunity (title, company, specialization, description, location, duration, skills_to_be_acquired, deadline, application_link, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", "sssssssssi", [$opp['title'], $opp['company'], $opp['specialization'], $opp['description'], $opp['location'], $opp['duration'], $skills, $opp['deadline'], $link, $_SESSION['user_id']]);
                flash("Opportunity added successfully");
            }
            header("Location: admin_dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $id > 0 ? "Edit" : "Add"; ?> Opportunity - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "admin_navbar.php"; ?>
<div class="container">
    <div class="form-box">
        <h2><?php echo $id > 0 ? "Edit Opportunity" : "Add Training Opportunity"; ?></h2>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="POST" action="admin_add_edit.php<?php echo $id > 0 ? "?id=" . $id : ""; ?>" novalidate>
            <label>Title</label>
            <input type="text" name="title" class="<?php echo ec('title', $errorFields); ?>" value="<?php echo h($opp['title']); ?>">
            <label>Company</label>
            <input type="text" name="company" class="<?php echo ec('company', $errorFields); ?>" value="<?php echo h($opp['company']); ?>">
            <label>Specialization (e.g. Information Technology)</label>
            <input type="text" name="specialization" class="<?php echo ec('specialization', $errorFields); ?>" value="<?php echo h($opp['specialization']); ?>">
            <label>Description</label>
            <textarea name="description" class="<?php echo ec('description', $errorFields); ?>"><?php echo h($opp['description']); ?></textarea>
            <label>Location</label>
            <input type="text" name="location" class="<?php echo ec('location', $errorFields); ?>" value="<?php echo h($opp['location']); ?>">
            <label>Duration</label>
            <input type="text" name="duration" class="<?php echo ec('duration', $errorFields); ?>" value="<?php echo h($opp['duration']); ?>">
            <label>Skills to be acquired (comma separated)</label>
            <input type="text" name="skills" class="<?php echo ec('skills', $errorFields); ?>" value="<?php echo h($opp['skills']); ?>">
            <label>Deadline</label>
            <input type="date" name="deadline" class="<?php echo ec('deadline', $errorFields); ?>" value="<?php echo h($opp['deadline']); ?>" <?php if ($id == 0) echo 'min="' . date('Y-m-d') . '"'; ?>>
            <label>Application link or email</label>
            <input type="text" name="apply_link" class="<?php echo ec('apply_link', $errorFields); ?>" value="<?php echo h($opp['apply_link']); ?>">
            <div class="form-actions">
                <a href="admin_dashboard.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><?php echo $id > 0 ? "Save" : "Add"; ?></button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
