<?php
$host = 'localhost';
$username = 'root';
$password = '1020';
$dbname = 'classes';

// Activer le rapport d'erreurs pour MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Création de la connexion
    $conn = new mysqli($host, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    echo "Connexion réussie !";

    // Make sure to pass $conn to other parts of your application as needed
    // For example:
    // include_once 'User.php';
    // $user = new User($conn);

} catch (mysqli_sql_exception $e) {
    // Gestion des erreurs MySQLi
    echo "Erreur MySQLi : " . $e->getMessage();

} catch (Exception $e) {
    // Gestion des autres erreurs
    echo "Erreur générale : " . $e->getMessage();

} // Do not close the connection here if you need to use it elsewhere
?>
