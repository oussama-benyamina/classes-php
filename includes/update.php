<?php
session_start();
require_once './User.php';
require_once './db-conn.php';

$user = new User($conn);
$message = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    
    if ($user->update($_SESSION['user_id'], $login, $password, $email, $firstname, $lastname)) {
        $message = "Profil mis à jour avec succès.";
        $_SESSION['login'] = $login; // Mettre à jour le login dans la session
    } else {
        $message = "Échec de la mise à jour du profil.";
    }
}

$userInfo = $user->getInfoById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Mettre à jour le profil</title>
</head>
<body>
    <h1>Mettre à jour le profil</h1>
    <?php if (isset($message)): ?>
        <div class="message <?php echo isset($success) && $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="login" placeholder="Login" value="<?php echo htmlspecialchars($userInfo['login']); ?>" required><br>
        <input type="password" name="password" placeholder="Nouveau mot de passe"><br>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" required><br>
        <input type="text" name="firstname" placeholder="Prénom" value="<?php echo htmlspecialchars($userInfo['firstname']); ?>" required><br>
        <input type="text" name="lastname" placeholder="Nom" value="<?php echo htmlspecialchars($userInfo['lastname']); ?>" required><br>
        <input type="submit" value="Mettre à jour">
    </form>
    <p><a href="../index.php">Retour à l'accueil</a></p>
</body>
</html>