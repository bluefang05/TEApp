<?php
require '../config/database.php';
require '../crudEmotions/Emotion.php';
require 'Image.php';
session_start();

try {
    $pdo = getPDO();
    $emotionHandler = new Emotion($pdo);
    $imageHandler = new Image($pdo, 'uploads/', 5000000);

    $selectedEmotionId = null;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $imagesPerPage = isset($_GET['imagesPerPage']) ? (int)$_GET['imagesPerPage'] : 12;

    if ($currentPage < 1) {
        $currentPage = 1;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['filter_emotion_id'])) {
            $selectedEmotionId = $_POST['filter_emotion_id'];
        } else {
            if (isset($_POST['new_image_emotion_id']) && isset($_FILES['new_image_file'])) {
                $fileCount = count($_FILES['new_image_file']['name']);

                for ($i = 0; $i < $fileCount; $i++) {
                    $file = [
                        'name' => $_FILES['new_image_file']['name'][$i],
                        'type' => $_FILES['new_image_file']['type'][$i],
                        'tmp_name' => $_FILES['new_image_file']['tmp_name'][$i],
                        'error' => $_FILES['new_image_file']['error'][$i],
                        'size' => $_FILES['new_image_file']['size'][$i]
                    ];

                    $emotionId = $_POST['new_image_emotion_id'][$i];

                    $imageHandler->create($file, $emotionId);
                }
            }

            if (isset($_POST['update_id'], $_POST['update_image_emotion_id'])) {
                $image = isset($_FILES['update_image_file']) ? $_FILES['update_image_file'] : null;
                $imageHandler->update($_POST['update_id'], $image, $_POST['update_image_emotion_id']);
            }

            if (isset($_POST['update_emotion'], $_POST['update_emotion_id'], $_POST['selected_images'])) {
                $newEmotionId = $_POST['update_emotion_id']; // New emotion to apply
                $selectedImages = $_POST['selected_images']; // Array of selected image ids

                // Update the emotion of each selected image
                foreach ($selectedImages as $imageId) {
                    $imageHandler->updateEmotion($imageId, $newEmotionId);
                }
            }

            if (isset($_POST['delete_selected'], $_POST['selected_images'])) {
                $selectedImages = $_POST['selected_images'];
                foreach ($selectedImages as $imageId) {
                    $imageHandler->delete($imageId);
                }
            }

            if (isset($_POST['delete_id'])) {
                $imageHandler->delete($_POST['delete_id']);
            }

            header('Location: ' . $_SERVER['PHP_SELF'] . '?page=' . $currentPage . '&imagesPerPage=' . $imagesPerPage);
            exit;
        }
    }

    $emotions = $emotionHandler->read();
    $totalPages = $imageHandler->getTotalPages($imagesPerPage);
    $images = $imageHandler->readWithEmotions($selectedEmotionId, $currentPage, $imagesPerPage, $_SESSION['user_id'], $_SESSION['role_id']);

    if ($currentPage > $totalPages && $totalPages > 0) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=' . $totalPages . '&imagesPerPage=' . $imagesPerPage);
        exit;
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <?php include_once '../header.php'; ?>

    <main>
        <section>
            <form method="post" class="filter-form">
                <div class="form-group">
                    <label for="filter_emotion_id">Filtrar por emoción:</label>
                    <select name="filter_emotion_id" id="filter_emotion_id" class="form-control">
                        <option value="">Mostrar todas</option>
                        <?php foreach ($emotions as $emotion): ?>
                                <option value="<?= $emotion['id']; ?>" <?= $selectedEmotionId == $emotion['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($emotion['emotion'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" value="Filtrar" class="btn btn-primary">
                </div>
            </form>
        </section>

        <section>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <div class="file-upload-container form-group">
                    <input type="file" name="new_image_file[]" multiple class="form-control">
                    <select name="new_image_emotion_id[]" class="form-control">
                        <?php foreach ($emotions as $emotion): ?>
                                <option value="<?= $emotion['id']; ?>"><?= htmlspecialchars($emotion['emotion'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" value="Agregar Imagen" class="btn btn-primary">
                </div>
            </form>
            <div>
                <button id="selectAll" class="btn btn-secondary">Seleccionar todo</button>
                <button id="invertSelection" class="btn btn-secondary">Invertir selección</button>
            </div>
        </section>

        <section>
            <form method="post" enctype="multipart/form-data" class="images-form">
                <select name="update_emotion_id" class="form-control">
                    <?php foreach ($emotions as $emotion): ?>
                            <option value="<?= $emotion['id']; ?>"><?= htmlspecialchars($emotion['emotion'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="update_emotion" value="Actualizar Emoción" class="btn btn-primary">
                <input type="submit" name="delete_selected" value="Eliminar seleccionados" class="btn btn-danger">

                <div class="image-container">
                    <?php foreach ($images as $image): ?>
                            <div class="image-item">
                                <img src="<?= htmlspecialchars($image['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen" class="img-thumbnail">
                                <div class="tag">
                                    <p><?= htmlspecialchars($emotionHandler->getEmotionName($image['emotion_id']), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <input type="checkbox" name="selected_images[]" value="<?= $image['id']; ?>">
                                <input type="hidden" name="update_ids[]" value="<?= $image['id']; ?>">

                                <form method="post" enctype="multipart/form-data" class="update-form">
        <input type="hidden" name="update_id" value="<?= $image['id']; ?>">
        <input type="file" name="update_image_file" class="form-control">
        <select name="update_image_emotion_id" class="form-control">
            <?php foreach ($emotions as $emotion): ?>
                    <option value="<?= $emotion['id']; ?>" <?= $image['emotion_id'] == $emotion['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($emotion['emotion'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Actualizar Imagen" class="btn btn-primary">
        <button type="submit" name="delete_id" value="<?= $image['id']; ?>" class="btn btn-danger">Eliminar</button>
    </form>
                            </div>
                    <?php endforeach; ?>
                </div>

                <section>
  <div class="pagination">
    <?php
    if ($_SESSION["role_id"] == 3) {
        $totalPages = $imageHandler->getTotalPages($imagesPerPage, $_SESSION["role_id"], $userId);
    } else {
        $totalPages = $imageHandler->getTotalPages($imagesPerPage);
    }

    for ($page = 1; $page <= $totalPages; $page++): ?>
           <a href="?page=<?= $page; ?>&imagesPerPage=<?= $imagesPerPage; ?>" class="<?= $page == $currentPage ? 'active' : ''; ?>"><?= $page; ?></a>
    <?php endfor; ?>
  </div>
</section>

        <section>
            <form method="get" class="images-per-page-form">
                <label for="images_per_page">Imágenes por página:</label>
                <select name="imagesPerPage" id="images_per_page" onchange="this.form.submit()">
                    <option value="12" <?= $imagesPerPage == 12 ? 'selected' : ''; ?>>12</option>
                    <option value="24" <?= $imagesPerPage == 24 ? 'selected' : ''; ?>>24</option>
                    <option value="48" <?= $imagesPerPage == 48 ? 'selected' : ''; ?>>48</option>
                    <option value="96" <?= $imagesPerPage == 96 ? 'selected' : ''; ?>>96</option>
                    <option value="120" <?= $imagesPerPage == 120 ? 'selected' : ''; ?>>120</option>
                </select>
            </form>
        </section>
            </form>
        </section>
    </main>

</body>
</html>
