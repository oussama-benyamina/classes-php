<?php
session_start();
require_once './includes/User.php';
require_once './includes/db-conn.php';

$user = new User($conn);
$isConnected = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Gestion des utilisateurs</title>
</head>
<body>
    <h1>Système de gestion des utilisateurs</h1>
    <?php if (isset($message)): ?>
        <div class="message <?php echo isset($success) && $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if ($isConnected): ?>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['login']); ?></p>
        <ul>
            <li><a href="./includes/update.php">Mettre à jour le profil</a></li>
            <li><a href="./includes/delete.php">Supprimer le compte</a></li>
            <li><a href="./includes/disconnect.php">Se déconnecter</a></li>
        </ul>
    <?php else: ?>
        <ul>
            <li><a href="./includes/connexion.php">Se connecter</a></li>
            <li><a href="./includes/register.php">S'inscrire</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>