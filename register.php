<?php
session_start();
include_once 'User.php';
include_once 'db-conn.php';

$user = new User($conn);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    
    if ($user->register($login, $password, $email, $firstname, $lastname)) {
        $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    } else {
        $message = "Échec de l'inscription. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="login" placeholder="Login" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="firstname" placeholder="Prénom" required><br>
        <input type="text" name="lastname" placeholder="Nom" required><br>
        <input type="submit" value="S'inscrire">
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>