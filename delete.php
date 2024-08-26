<?php
session_start();
require_once 'User.php';
require_once 'db-conn.php';

$user = new User($conn);
$message = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($user->delete($_SESSION['user_id'])) {
        session_destroy();
        $message = "Compte supprimé avec succès.";
        header("Location: index.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Échec de la suppression du compte.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer le compte</title>
</head>
<body>
    <h1>Supprimer le compte</h1>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
    <form method="post" action="">
        <input type="submit" value="Supprimer le compte">
    </form>
    <p><a href="index.php">Annuler et retourner à l'accueil</a></p>
</body>
</html>