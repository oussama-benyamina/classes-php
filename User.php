<?php
class User {
    private $id;
    private $login;
    private $password;
    private $email;
    private $firstname;
    private $lastname;
    private $conn;
    private $isConnected = false; // Indicate if the user is connected

    // Constructor to initialize the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Register a new user
    public function register($login, $password, $email, $firstname, $lastname) {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        
        $passwordHash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssss", $this->login, $passwordHash, $this->email, $this->firstname, $this->lastname);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            return $this->getAllInfos();
        } else {
            return false;
        }
    }

    // Connect a user
    public function connect($login, $password) {
        $query = "SELECT * FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->login = $row['login'];
                $this->email = $row['email'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];
                $this->isConnected = true;
                return true;
            }
        }
        return false;
    }

    // Disconnect a user
    public function disconnect() {
        $this->isConnected = false;
    }

    // Delete a user
    public function delete() {
        if ($this->isConnected) {
            $query = "DELETE FROM utilisateurs WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $this->id);

            if ($stmt->execute()) {
                $this->disconnect(); // Disconnect after deletion
                return true;
            } else {
                echo "Delete failed: " . $stmt->error; // Debug message
            }
        } else {
            echo "User not connected"; // Debug message
        }
        return false;
    }

    // Update user information
    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->isConnected) {
            $this->login = $login;
            $this->password = $password;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            $query = "UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }
            
            $passwordHash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bind_param("sssssi", $this->login, $passwordHash, $this->email, $this->firstname, $this->lastname, $this->id);

            return $stmt->execute();
        }
        return false;
    }

    // Check if user is connected
    public function isConnected() {
        return $this->isConnected;
    }

    // Get all user information
    public function getAllInfos() {
        if ($this->isConnected) {
            return [
                'id' => $this->id,
                'login' => $this->login,
                'email' => $this->email,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
            ];
        }
        return false;
    }

    // Get user login
    public function getLogin() {
        return $this->login;
    }

    // Get user email
    public function getEmail() {
        return $this->email;
    }

    // Get user first name
    public function getFirstname() {
        return $this->firstname;
    }

    // Get user last name
    public function getLastname() {
        return $this->lastname;
    }
}
?>
