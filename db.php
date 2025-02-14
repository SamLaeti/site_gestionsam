<?php
$host = 'localhost'; // Adresse de votre serveur de base de données
$db = 'c6nr0dwpc_basedonneev2'; // Nom de votre base de données
$user = 'c6nr0dwpc_basedonneev2'; // Nom d'utilisateur de la base de données
$pass = 'samlaetiv2'; // Mot de passe de la base de données
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>