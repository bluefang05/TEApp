<?php
class Image {
    private $pdo;
    private $uploadPath;
    private $maxFileSize;

    public function __construct($pdo, $uploadPath = 'uploads/', $maxFileSize = 5000000) {
        $this->pdo = $pdo;
        $this->uploadPath = $uploadPath;
        $this->maxFileSize = $maxFileSize;
    }

    private function validateImage($image) {
        if (empty($image['name'])) {
            throw new Exception('No file selected');
        }

        $validImageTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
        $imageType = exif_imagetype($image['tmp_name']);

        if ($image['size'] > $this->maxFileSize) {
            throw new Exception('The image is too big');
        }

        if (!in_array($imageType, $validImageTypes)) {
            throw new Exception('The file type is not supported');
        }
    }

    private function generateUniqueFileName($extension) {
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        $fileName = $timestamp . '_' . $randomString . '.' . $extension;
        return $fileName;
    }

    public function create($image, $emotionId) {
        $this->validateImage($image);
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $this->generateUniqueFileName($extension);
        $filePath = $this->uploadPath . $fileName;

        if (move_uploaded_file($image['tmp_name'], $filePath)) {
            $sql = "INSERT INTO Images (image_path, emotion_id) VALUES (?, ?)";
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$filePath, $emotionId]);
            } catch (PDOException $e) {
                throw new Exception('Database Error: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Error uploading the image');
        }
    }

    public function read($page = 1, $imagesPerPage = 12) {
        $limit = $imagesPerPage;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM Images ORDER BY emotion_id ASC LIMIT $limit OFFSET $offset";
        try {
            return $this->pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    public function getTotalPages($imagesPerPage = 12) {
        $sql = "SELECT COUNT(*) AS total FROM Images";
        try {
            $result = $this->pdo->query($sql)->fetch();
            $totalImages = $result['total'];
            return ceil($totalImages / $imagesPerPage);
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    public function readWithEmotions($emotionId = null, $page = 1, $imagesPerPage = 12) {
        $limit = $imagesPerPage;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT Images.*, Emotions.emotion as emotion_name 
                FROM Images 
                LEFT JOIN Emotions ON Images.emotion_id = Emotions.id ";
    
        if ($emotionId) {
            $sql .= " WHERE Images.emotion_id = :emotionId";
        }
        
        $sql .= " ORDER BY Images.id ASC LIMIT $limit OFFSET $offset";
    
        try {
            $stmt = $this->pdo->prepare($sql);
    
            if ($emotionId) {
                $stmt->bindValue(':emotionId', $emotionId);
            }
    
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    public function getImagesWithUnknownEmotion($page = 1, $imagesPerPage = 12) {
        $limit = $imagesPerPage;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT Images.* FROM Images 
                LEFT JOIN Emotions ON Images.emotion_id = Emotions.id
                WHERE Emotions.id IS NULL 
                ORDER BY Images.id ASC LIMIT $limit OFFSET $offset";
    
        try {
            return $this->pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    public function update($id, $image, $emotionId) {
        if (!empty($image['name'])) {
            $this->validateImage($image);
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = $this->generateUniqueFileName($extension);
            $filePath = $this->uploadPath . $fileName;
            if (!move_uploaded_file($image['tmp_name'], $filePath)) {
                throw new Exception('Error uploading the image');
            }
        } else {
            $sql = "SELECT image_path FROM Images WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            $filePath = $result['image_path'];
        }

        $sql = "UPDATE Images SET image_path=?, emotion_id=? WHERE id=?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$filePath, $emotionId, $id]);
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }

    public function updateEmotion($id, $emotionId) {
        $sql = "UPDATE Images SET emotion_id = :emotionId WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['emotionId' => $emotionId, 'id' => $id]);
    }

    public function delete($id) {
        $sql = "SELECT image_path FROM Images WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result) {
            if (file_exists($result['image_path'])) {
                unlink($result['image_path']);
            }
            $sql = "DELETE FROM Images WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        } else {
            throw new Exception('Image not found: ' . $id);
        }
    }
}
?>
