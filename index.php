<?php
require 'db.php';
require 'functions.php';
session_start();
redirectIfNotLoggedIn();

$updateSuccess = isset($_GET['update']) && $_GET['update'] == 'success';

$stmt = $pdo->prepare("
    SELECT numero_dossier, nom_client 
    FROM dossiers 
    WHERE ce LIKE '%véhicule terminé%' 
    ORDER BY id DESC 
    LIMIT 10
");
$stmt->execute();
$vehicules_termines = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Accueil</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="create_dossier.php">Créer un nouveau dossier</a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Gérer les utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <?php if ($updateSuccess): ?>
            <div id="notification" class="alert alert-success" role="alert">
                Mise à jour réussie !
            </div>
        <?php endif; ?>
        <h1>Bienvenue, <?php echo $_SESSION['username']; ?>!</h1>
        <form class="mb-4">
            <input type="text" class="form-control" name="query" placeholder="Rechercher un dossier..." autocomplete="off">
            <ul id="results-list" class="list-group mt-2"></ul>
        </form>
        <h2>Véhicules terminés</h2>
        <ul class="list-group">
            <?php foreach ($vehicules_termines as $vehicule): ?>
                <li class="list-group-item">Dossier n°<?php echo $vehicule['numero_dossier']; ?> - <?php echo $vehicule['nom_client']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/search_dossier.js"></script>
    <script src="js/notification.js"></script>
</body>
</html>