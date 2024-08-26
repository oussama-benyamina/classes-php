<?php
// User.php

$host = 'localhost';
$username = 'root';
$password = '1020';
$dbname = 'classes';


class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;
    private $isConnected = false;


    public function __construct($host, $username, $password, $dbname) {
        // Initialiser la connexion à la base de données
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        // Hash du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Préparer la requête
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $stmt->close();
            return $this->getAllInfos();
        } else {
            $stmt->close();
            return false;
        }
    }

    public function connect($login, $password) {
        // Préparer la requête
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                $this->isConnected = true;
                $stmt->close();
                return true;
            }
        }
        $stmt->close();
        return false;
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
            $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $result = $stmt->execute();
            $stmt->close();
            $this->disconnect();
            return $result;
        }
        return false;
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            // Hash du nouveau mot de passe
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);

            if ($stmt->execute()) {
                // Mettre à jour les attributs de l'objet
                $this->login = $login;
                $this->email = $email;
                $this->firstname = $firstname;
                $this->lastname = $lastname;
                $stmt->close();
                return true;
            } else {
                $stmt->close();
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
        $this->conn->close();
    }
}
?>
