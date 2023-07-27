<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function create($username, $hashed_password) {
        $sql = "INSERT INTO Users (username, hashed_password) VALUES (:username, :hashed_password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'hashed_password' => $hashed_password]);
    }

    // Read
    public function read() {
        $sql = "SELECT * FROM Users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update
    public function update($id, $username, $hashed_password) {
        $sql = "UPDATE Users SET username = :username, hashed_password = :hashed_password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'username' => $username, 'hashed_password' => $hashed_password]);
    }

    // Delete
    public function delete($id) {
        $sql = "DELETE FROM Users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
?>
