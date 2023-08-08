<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudImages/crudImages.php");
        exit; // Always call exit after sending a Location header.
    }
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <title>Emotion/Image Management System</title>
    <link rel="stylesheet" href="https://aspierd.com/otros/widgets/api/selecciona-2/backend/header-style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <?php if($_SESSION['role_id'] != 3): ?>
                <li><a href="https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudEmotions/crudEmotions.php">Emotions</a></li>
            <?php endif; ?>
            <li><a href="https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudImages/crudImages.php">Images</a></li>
            <?php if($_SESSION['role_id'] == 2): ?>
                <li><a href="https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudUsers/crudUser.php">Users</a></li>
            <?php endif; ?>
            <?php
                if (isset($_SESSION['username'])) {
                    echo "<li class='welcome'>Welcome, {$_SESSION['username']}</li>";
                    echo '<li class="logout"><a href="https://aspierd.com/otros/widgets/api/selecciona-2/backend/session/logout.php">Logout</a></li>';
                }
            ?>
        </ul>
    </nav>
</header>

    <?php echo var_dump($_SESSION)?>
</body>
</html>
