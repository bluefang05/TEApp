<?php
if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3)) {
    // Redirect the user to the specified URL
    header("Location: https://aspierd.com/otros/widgets/api/selecciona-2/backend/crudImages/crudImages.php");
    exit; // Make sure to exit after the redirect to prevent further code execution
}

require_once '../config/database.php';

require_once 'User.php';





    error_reporting(E_ALL);

    ini_set('display_errors', 1);



// Función para manejar los errores

function errorHandler($severity, $message, $file, $line) {

    if (error_reporting() & $severity) {

        echo "<strong>Error:</strong><br>";

        echo "Severity: $severity<br>";

        echo "Message: $message<br>";

        echo "File: $file<br>";

        echo "Line: $line<br>";

    }

}



// Registrar la función de manejo de errores

set_error_handler('errorHandler');



try {

    $pdo = getPDO();

    $userHandler = new User($pdo);



    // Retrieve the roles for the select input

    $roleHandler = new Role($pdo);

    $roles = $roleHandler->read();



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST['new_username']) && isset($_POST['new_password']) && isset($_POST['role_id'])) {

            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $userHandler->create($_POST['new_username'], $hashed_password, $_POST['role_id']);

        }



        if (isset($_POST['update_id']) && isset($_POST['update_username']) && isset($_POST['update_password']) && isset($_POST['role_id'])) {

            $hashed_password = password_hash($_POST['update_password'], PASSWORD_DEFAULT);

            $userHandler->update($_POST['update_id'], $_POST['update_username'], $hashed_password, $_POST['role_id']);

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



        <!-- Update the role input to a select input -->

        <select name="role_id">

            <?php foreach ($roles as $role): ?>

                <option value="<?= $role['id']; ?>"><?= $role['role_name']; ?></option>

            <?php endforeach; ?>

        </select>



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



                    <!-- Update the role input to a select input -->

                    <select name="role_id">

                        <?php foreach ($roles as $role): ?>

                            <option value="<?= $role['id']; ?>" <?php if ($user['role_id'] == $role['id']) echo 'selected'; ?>>

                                <?= $role['role_name']; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>



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

