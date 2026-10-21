<?php
require_once "admin_auth.php";
$results = fetch_all($conn, "SELECT * FROM opportunity ORDER BY opportunity_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "admin_navbar.php"; ?>
<div class="container">
    <?php include "message.php"; ?>
    <div class="top-bar">
        <h2>Manage Opportunities</h2>
        <a href="admin_add_edit.php" class="btn btn-primary">+ Add Opportunity</a>
    </div>
    <?php if (count($results) == 0) { ?>
        <div class="empty-state">No opportunities have been added yet</div>
    <?php } else { ?>
    <table>
        <tr>
            <th>Title</th>
            <th>Company</th>
            <th>Specialization</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($results as $opp) { ?>
        <tr>
            <td><?php echo h($opp['title']); ?></td>
            <td><?php echo h($opp['company']); ?></td>
            <td><?php echo h($opp['specialization']); ?></td>
            <td><?php echo h($opp['deadline']); ?></td>
            <td>
                <a href="admin_add_edit.php?id=<?php echo $opp['opportunity_id']; ?>">Edit</a>
                <button type="button" class="delete-btn" data-id="<?php echo $opp['opportunity_id']; ?>" data-title="<?php echo h($opp['title']); ?>">Delete</button>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>
</div>

<div class="modal" id="delete-modal">
    <div class="modal-box">
        <div class="modal-icon">!</div>
        <h3>Delete this opportunity?</h3>
        <p>Are you sure you want to delete "<strong id="delete-title"></strong>"?<br>This action can't be undone and you won't be able to recover it.</p>
        <form method="POST" action="admin_delete.php" class="modal-actions">
            <input type="hidden" name="id" id="delete-id" value="">
            <button type="button" class="btn btn-outline" id="delete-cancel">Cancel</button>
            <button type="submit" class="btn btn-danger">Yes, delete</button>
        </form>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
