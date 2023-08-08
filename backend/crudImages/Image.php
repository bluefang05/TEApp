<?php
class Image {
    private $pdo;
    private $uploadPath;
    private $maxFileSize;

    public function __construct($pdo, $uploadPath = 'uploads/', $maxFileSize = 5000000) {
        session_start();
        $this->pdo = $pdo;
        $this->uploadPath = $uploadPath;
        $this->maxFileSize = $maxFileSize;
    }

    // Function to validate the image
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

    // Function to generate a unique filename
    private function generateUniqueFileName($extension) {
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        $fileName = $timestamp . '_' . $randomString . '.' . $extension;
        return $fileName;
    }

    // Function to create an image entry
    public function create($image, $emotionId = null) {
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = $_SESSION['role_id'] ?? null;

        $this->validateImage($image);
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $this->generateUniqueFileName($extension);
        $filePath = $this->uploadPath . $fileName;

        if($roleId == 3){
            $userIdToUse = $userId;
        }else{
            $userIdToUse = null;
        }

        if (move_uploaded_file($image['tmp_name'], $filePath)) {
            $sql = "INSERT INTO Images (image_path, emotion_id, user_id) VALUES (:filePath, :emotionId, :userIdToUse)";
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':filePath' => $filePath, ':emotionId' => $emotionId, ':userIdToUse' => $userIdToUse]);
            } catch (PDOException $e) {
                throw new Exception('Database Error: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Error uploading the image');
        }
    }

    // Function to read images
    public function read($page = 1, $imagesPerPage = 12, $roleId = null, $userId = null) {
        $limit = $imagesPerPage;
        $offset = ($page - 1) * $limit; 
        $sql = "SELECT * FROM Images";
    
        if($roleId == 3) {
           $sql .= " WHERE user_id = :userId";  
        } else {
           $sql .= " WHERE user_id IS NULL";  
        }  
     
        $sql .= " ORDER BY id ASC LIMIT $limit OFFSET $offset";
            
        $stmt = $this->pdo->prepare($sql);
        
        if($roleId == 3) {
           $stmt->bindValue(':userId', $userId);
        }
            
        $stmt->execute();
            
        return $stmt->fetchAll();  
    }

    public function getTotalPages($imagesPerPage = 12, $roleId = null, $userId = null) {
        if ($roleId == 3) {
            $sql = "SELECT COUNT(*) AS total FROM Images WHERE user_id = :userId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':userId', $userId);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM Images WHERE user_id IS NULL";
            $stmt = $this->pdo->prepare($sql);
        }
    
        $stmt->execute();
        $result = $stmt->fetch();
        $totalImages = $result['total'];
    
        return ceil($totalImages / $imagesPerPage);
    }

    public function readWithEmotions($emotionId = null, $page = 1, $imagesPerPage = 12, $userId = null, $roleId = null) {
        $limit = $imagesPerPage;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT Images.*, Emotions.emotion as emotion_name 
                FROM Images 
                LEFT JOIN Emotions ON Images.emotion_id = Emotions.id";
    
        $conditions = [];
    
        if ($emotionId) {
            $conditions[] = "Images.emotion_id = :emotionId";
        }
    
        if ($roleId == 3) {
            $conditions[] = "Images.user_id = :userId";
        } else {
            $conditions[] = "Images.user_id IS NULL";
        }
    
        if (count($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Images.id ASC LIMIT $limit OFFSET $offset";
    
        try {
            $stmt = $this->pdo->prepare($sql);
    
            if ($emotionId) {
                $stmt->bindValue(':emotionId', $emotionId);
            }
    
            if ($roleId == 3) {
                $stmt->bindValue(':userId', $userId);
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

    public function update($id, $image, $emotionId = null) {
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

    public function delete($id) {
        $sql = "DELETE FROM Images WHERE id=?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception('Database Error: ' . $e->getMessage());
        }
    }
}
?>
