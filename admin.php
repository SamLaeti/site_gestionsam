<?php
require 'db.php';
require 'functions.php';
redirectIfNotLoggedIn();

if (!isAdmin()) {
    die('Accès refusé');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Créer un nouvel utilisateur</h2>
    <form method="post" action="admin.php">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <label for="role">Rôle:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="visiteur">Visiteur</option>
            <option value="technicien">Technicien</option>
            <option value="ccs">CCS</option>
        </select>
        <button type="submit">Créer</button>
    </form>
    <h2>Liste des utilisateurs</h2>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?php echo $user['username']; ?> - <?php echo $user['role']; ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>