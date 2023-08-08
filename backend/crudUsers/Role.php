<?php
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
