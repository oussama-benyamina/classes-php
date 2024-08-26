<?php
class User {
    private $id;
    private $login;
    private $password;
    private $email;
    private $firstname;
    private $lastname;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getId() {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $login, $passwordHash, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            return true;
        }
        return false;
    }

    public function connect($login, $password) {
        $query = "SELECT * FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                return true;
            }
        }
        return false;
    }

    public function delete($userId) {
        $query = "DELETE FROM utilisateurs WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function update($userId, $login, $password, $email, $firstname, $lastname) {
        $query = "UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ?";
        $params = [$login, $email, $firstname, $lastname];
        $types = "ssss";

        if (!empty($password)) {
            $query .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = $userId;
        $types .= "i";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function getInfoById($id) {
        $query = "SELECT id, login, email, firstname, lastname FROM utilisateurs WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>