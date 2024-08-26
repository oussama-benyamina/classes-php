<?php
session_start();
require_once 'User.php';
require_once 'db-conn.php';

$user = new User($conn);
$isConnected = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
</head>
<body>
    <h1>Système de gestion des utilisateurs</h1>
    <?php if ($isConnected): ?>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['login']); ?></p>
        <ul>
            <li><a href="update.php">Mettre à jour le profil</a></li>
            <li><a href="delete.php">Supprimer le compte</a></li>
            <li><a href="disconnect.php">Se déconnecter</a></li>
        </ul>
    <?php else: ?>
        <ul>
            <li><a href="connexion.php">Se connecter</a></li>
            <li><a href="register.php">S'inscrire</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>