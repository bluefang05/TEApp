<?php
class Emotion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($emotion) {
        $sql = "INSERT INTO Emotions (emotion) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$emotion]);
    }

    public function read() {
        $sql = "SELECT * FROM Emotions";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function update($id, $emotion) {
        $sql = "UPDATE Emotions SET emotion=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$emotion, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM Emotions WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
    }

    public function getEmotionName($emotionId) {
        $sql = "SELECT emotion FROM Emotions WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$emotionId]);
        $result = $stmt->fetch();

        if ($result) {
            return $result['emotion'];
        } else {
            return 'Unknown Emotion';
        }
    }
}
?>
