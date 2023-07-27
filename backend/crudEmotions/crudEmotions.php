<?php
session_start();



require_once '../config/database.php';
require_once './Emotion.php';

try {
    $pdo = getPDO();
    $emotionHandler = new Emotion($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['new_emotion'])) {
            $emotionHandler->create($_POST['new_emotion']);
        }

        if (isset($_POST['update_id']) && isset($_POST['update_emotion'])) {
            $emotionHandler->update($_POST['update_id'], $_POST['update_emotion']);
        }

        if (isset($_POST['delete_id'])) {
            $emotionHandler->delete($_POST['delete_id']);
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $emotions = $emotionHandler->read();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php include_once '../header.php'; ?>

<div class="container">
    <!-- HTML for the creation form -->
    <form method="post">
        <input type="text" name="new_emotion" placeholder="New emotion">
        <input type="submit" value="Add">
    </form>

    <!-- HTML for the list of emotions -->
    <?php if (!empty($emotions)): ?>
        <?php foreach ($emotions as $emotion): ?>
            <div class="emotion-item">
                <?= $emotion['emotion']; ?>
                <!-- Form for updating an emotion -->
                <form method="post">
                    <input type="hidden" name="update_id" value="<?= $emotion['id']; ?>">
                    <input type="text" name="update_emotion" value="<?= $emotion['emotion']; ?>">
                    <input type="submit" value="Update">
                </form>
                <!-- Form for deleting an emotion -->
                <form method="post">
                    <input type="hidden" name="delete_id" value="<?= $emotion['id']; ?>">
                    <input type="submit" value="Delete">
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No emotions found.</p>
    <?php endif; ?>
</div>
</body>
</html>
