<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: https://aspierd.com/otros/widgets/api/selecciona-1/session/login.php");
        exit; // Always call exit after sending a Location header.
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Emotion/Image Management System</title>
    <link rel="stylesheet" href="https://aspierd.com/otros/widgets/api/selecciona-1/header-style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="https://aspierd.com/otros/widgets/api/selecciona-1/crudEmotions/crudEmotions.php">Emotions</a></li>
                <li><a href="https://aspierd.com/otros/widgets/api/selecciona-1/crudImages/crudImages.php">Images</a></li>
                <li><a href="https://aspierd.com/otros/widgets/api/selecciona-1/session/crudUser.php">Users</a></li>
                <?php
                    if (isset($_SESSION['username'])) {
                        echo "<li class='welcome'>Welcome, {$_SESSION['username']}</li>";
                        echo '<li class="logout"><a href="https://aspierd.com/otros/widgets/api/selecciona-1/session/logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
        </nav>
    </header>
</body>
</html>
