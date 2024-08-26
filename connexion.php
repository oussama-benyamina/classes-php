<?php
session_start();
require_once 'User.php';
require_once 'db-conn.php';

$user = new User($conn);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    if ($user->connect($login, $password)) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['login'] = $user->getLogin();
        header("Location: index.php");
        exit();
    } else {
        $message = "Échec de la connexion. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="login" placeholder="Login" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="submit" value="Se connecter">
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>