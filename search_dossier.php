<?php
require 'db.php';
require 'functions.php';
redirectIfNotLoggedIn();

$query = '%' . $_GET['query'] . '%';

$stmt = $pdo->prepare("
    SELECT d.*, 
           CONCAT('CE: ', d.ce, ' | CSS: ', d.css, ' | Technicien: ', d.technicien) AS derniere_saisie 
    FROM dossiers d 
    WHERE d.numero_dossier LIKE :query1 
       OR d.numero_cles LIKE :query2 
       OR d.nom_client LIKE :query3
");

$stmt->execute([
    ':query1' => $query,
    ':query2' => $query,
    ':query3' => $query
]);

$dossiers = $stmt->fetchAll();

if (isset($_GET['ajax'])) {
    foreach ($dossiers as $dossier) {
        echo "<li class='list-group-item'>
                <a href='edit_dossier.php?id={$dossier['id']}'>
                    Dossier n°{$dossier['numero_dossier']} - {$dossier['nom_client']} - Clés: {$dossier['numero_cles']}
                </a>
                <p>Dernière saisie: {$dossier['derniere_saisie']}</p>
              </li>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recherche de dossier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Accueil</a>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Résultats de la recherche</h2>
        <ul id="results-list" class="list-group">
            <?php foreach ($dossiers as $dossier): ?>
                <li class="list-group-item">
                    <a href="edit_dossier.php?id=<?php echo $dossier['id']; ?>">
                        Dossier n°<?php echo $dossier['numero_dossier']; ?> - <?php echo $dossier['nom_client']; ?> - Clés: <?php echo $dossier['numero_cles']; ?>
                    </a>
                    <p>Dernière saisie: <?php echo $dossier['derniere_saisie']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php" class="btn btn-primary mt-3">Retour à l'accueil</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/search_dossier.js"></script>
</body>
</html>