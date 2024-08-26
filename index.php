<?php
include_once 'db-conn.php';
include_once 'User.php';

// Instantiate the User class
$user = new User($conn);

// Variables for messages and user info
$message = '';
$userInfo = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Register
    if (isset($_POST['register'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $result = $user->register($login, $password, $email, $firstname, $lastname);
        $message = $result ? "User registered successfully.<br>" : "Failed to register user.<br>";
    }

    // Connect
    if (isset($_POST['connect'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];
        if ($user->connect($login, $password)) {
            $message = "User connected successfully.<br>";
        } else {
            $message = "Failed to connect user.<br>";
        }
    }

    // Disconnect
    if (isset($_POST['disconnect'])) {
        $user->disconnect();
        $message = "User disconnected successfully.<br>";
    }

    // Update
    if (isset($_POST['update'])) {
        if ($user->isConnected()) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            if ($user->update($login, $password, $email, $firstname, $lastname)) {
                $message = "User updated successfully.<br>";
            } else {
                $message = "Failed to update user.<br>";
            }
        } else {
            $message = "You must be connected to update your information.<br>";
        }
    }

    // Delete
    if (isset($_POST['delete'])) {
        if ($user->isConnected()) {
            if ($user->delete()) {
                $message = "User deleted successfully.<br>";
            } else {
                $message = "Failed to delete user.<br>";
            }
        } else {
            $message = "You must be connected to delete your account.<br>";
        }
    }
}

// Display user info
$userInfo = $user->isConnected() ? $user->getAllInfos() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>
    <h1>User Management System</h1>

    <!-- Registration Form -->
    <form method="post" action="">
        <h2>Register</h2>
        <input type="text" name="login" placeholder="Login" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="firstname" placeholder="First Name" required><br>
        <input type="text" name="lastname" placeholder="Last Name" required><br>
        <input type="submit" name="register" value="Register">
    </form>

    <!-- Login Form -->
    <form method="post" action="">
        <h2>Connect</h2>
        <input type="text" name="login" placeholder="Login" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="connect" value="Connect">
    </form>

    <!-- Update Form -->
    <?php if ($user->isConnected()) : ?>
        <form method="post" action="">
            <h2>Update</h2>
            <input type="text" name="login" placeholder="New Login" required><br>
            <input type="password" name="password" placeholder="New Password" required><br>
            <input type="email" name="email" placeholder="New Email" required><br>
            <input type="text" name="firstname" placeholder="New First Name" required><br>
            <input type="text" name="lastname" placeholder="New Last Name" required><br>
            <input type="submit" name="update" value="Update">
        </form>

        <!-- Delete Form -->
        <form method="post" action="">
            <h2>Delete</h2>
            <input type="submit" name="delete" value="Delete">
        </form>
    <?php endif; ?>

    <!-- Disconnect Form -->
    <form method="post" action="">
        <h2>Disconnect</h2>
        <input type="submit" name="disconnect" value="Disconnect">
    </form>

    <!-- Display Messages -->
    <?php if (!empty($message)) : ?>
        <div><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Display User Info -->
    <?php if ($userInfo) : ?>
        <h2>User Information</h2>
        <p>Login: <?php echo htmlspecialchars($userInfo['login']); ?></p>
        <p>Email: <?php echo htmlspecialchars($userInfo['email']); ?></p>
        <p>First Name: <?php echo htmlspecialchars($userInfo['firstname']); ?></p>
        <p>Last Name: <?php echo htmlspecialchars($userInfo['lastname']); ?></p>
    <?php else : ?>
        <p>No user is currently logged in.</p>
    <?php endif; ?>
</body>
</html>
