<?php
class Userpdo {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':login' => $login,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname
        ]);

        if ($stmt->rowCount() > 0) {
            $this->id = $this->pdo->lastInsertId();
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            return $this->getAllInfos();
        }
        return false;
    }

    public function connect($login, $password) {
        $query = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            return true;
        }
        return false;
    }

    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function delete() {
        if ($this->id) {
            $query = "DELETE FROM utilisateurs WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute([':id' => $this->id]);
            if ($result) {
                $this->disconnect();
                return true;
            }
        }
        return false;
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            $query = "UPDATE utilisateurs SET login = :login, email = :email, firstname = :firstname, lastname = :lastname";
            $params = [
                ':login' => $login,
                ':email' => $email,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':id' => $this->id
            ];

            if (!empty($password)) {
                $query .= ", password = :password";
                $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $query .= " WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);

            if ($result) {
                $this->login = $login;
                $this->email = $email;
                $this->firstname = $firstname;
                $this->lastname = $lastname;
                return true;
            }
        }
        return false;
    }

    public function isConnected() {
        return $this->id !== null;
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
        return false;
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
}
?>