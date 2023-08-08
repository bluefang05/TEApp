<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function create($username, $hashed_password, $role_id) {
        $sql = "INSERT INTO Users (username, hashed_password, role_id) VALUES (:username, :hashed_password, :role_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'hashed_password' => $hashed_password, 'role_id' => $role_id]);
    }

    // Read
    public function read() {
        $sql = "SELECT * FROM Users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Search by username
    public function searchByUsername($username) {
        $sql = "SELECT * FROM Users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    // Update
    public function update($id, $username, $hashed_password, $role_id) {
        $sql = "UPDATE Users SET username = :username, hashed_password = :hashed_password, role_id = :role_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'username' => $username, 'hashed_password' => $hashed_password, 'role_id' => $role_id]);
    }

    // Delete
    public function delete($id) {
        $sql = "DELETE FROM Users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}

class Role {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function create($role_name) {
        $sql = "INSERT INTO Roles (role_name) VALUES (:role_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role_name' => $role_name]);
    }

    // Read
    public function read() {
        $sql = "SELECT * FROM Roles";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update
    public function update($id, $role_name) {
        $sql = "UPDATE Roles SET role_name = :role_name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'role_name' => $role_name]);
    }

    // Delete
    public function delete($id) {
        $sql = "DELETE FROM Roles WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
?>
