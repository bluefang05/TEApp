<?php
require_once '../config/database.php';
require_once 'User.php';

try {
    $pdo = getPDO();
    $userHandler = new User($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['new_username']) && isset($_POST['new_password'])) {
            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $userHandler->create($_POST['new_username'], $hashed_password);
        }

        if (isset($_POST['update_id']) && isset($_POST['update_username']) && isset($_POST['update_password'])) {
            $hashed_password = password_hash($_POST['update_password'], PASSWORD_DEFAULT);
            $userHandler->update($_POST['update_id'], $_POST['update_username'], $hashed_password);
        }

        if (isset($_POST['delete_id'])) {
            $userHandler->delete($_POST['delete_id']);
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $users = $userHandler->read();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="usercrud-style.css">
</head>
<body>
<?php include_once '../header.php'; ?>

    <div class="container">
        <!-- HTML for the creation form -->
        <form method="post">
            <input type="text" name="new_username" placeholder="New username">
            <input type="password" name="new_password" placeholder="New password">
            <input type="submit" value="Add">
        </form>

        <!-- HTML for the list of users -->
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <div class="user-item">
                    <?= $user['username']; ?>
                    <!-- Form for updating a user -->
                    <form method="post">
                        <input type="hidden" name="update_id" value="<?= $user['id']; ?>">
                        <input type="text" name="update_username" value="<?= $user['username']; ?>">
                        <input type="password" name="update_password" placeholder="New password">
                        <input type="submit" value="Update">
                    </form>
                    <!-- Form for deleting a user -->
                    <form method="post">
                        <input type="hidden" name="delete_id" value="<?= $user['id']; ?>">
                        <input type="submit" value="Delete">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
