<?php
session_start();

require_once '../config/database.php';
require_once '../crudUsers/User.php';

$messages = [];

if (isset($_POST['username']) && isset($_POST['password'])) {
    try {
        $pdo = getPDO();
        $userHandler = new User($pdo);

        $users = $userHandler->read();

        foreach ($users as $user) {
            if ($user['username'] == $_POST['username'] && password_verify($_POST['password'], $user['hashed_password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id']; // add role_id to the session
                $_SESSION['user_id'] = $user['id']; // add user_id to the session
                $messages[] = 'Successfully logged in! Redirecting...';
                header('Location: https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudEmotions/crudEmotions.php'); // redirect to the crudImages page
                exit;
            }
        }

        $messages[] = 'Incorrect username or password.';
    } catch (PDOException $e) {
        $messages[] = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="login-styles.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <!-- HTML for the login form -->
        <form method="post" class="login-form">
            <div class="form-field">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" class="form-input">
            </div>
            <div class="form-field">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" class="form-input">
            </div>
            <div class="form-field">
                <input type="submit" value="Log In" class="form-input submit-btn">
            </div>
        </form>

        <!-- Display messages -->
        <section class="messages-section">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <p class="message-text"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
