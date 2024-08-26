<?php
// user-pdo.php

class Userpdo {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;
    private $isConnected = false;

    public function __construct($host, $username, $password, $dbname) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)");
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->execute();

            $this->id = $this->conn->lastInsertId();
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return $this->getAllInfos();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function connect($login, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = :login");
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                $this->isConnected = true;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function disconnect() {
        $this->isConnected = false;
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function delete() {
        if ($this->id) {
            try {
                $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
                $result = $stmt->execute();
                $this->disconnect();
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }
        return false;
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id");
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':firstname', $firstname);
                $stmt->bindParam(':lastname', $lastname);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
                $stmt->execute();

                $this->login = $login;
                $this->email = $email;
                $this->firstname = $firstname;
                $this->lastname = $lastname;

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        return false;
    }

    public function isConnected() {
        return $this->isConnected;
    }

    public function getAllInfos() {
        if ($this->id) {
            return [
                'id' => $this->id,
                'login' => $this->login,
                'email' => $this->email,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname
            ];
        }
        return null;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function __destruct() {
        $this->conn = null;
    }
}
?>
