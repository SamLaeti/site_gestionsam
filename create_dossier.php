<?php
require 'db.php';
require 'functions.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_dossier = $_POST['numero_dossier'];
    $numero_cles = $_POST['numero_cles'];
    $nom_client = $_POST['nom_client'];
    $ce = $_POST['ce'];
    $css = $_POST['css'];
    $technicien = $_POST['technicien'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO dossiers (numero_dossier, numero_cles, nom_client, ce, css, technicien, created_by) VALUES (:numero_dossier, :numero_cles, :nom_client, :ce, :css, :technicien, :created_by)");
    $stmt->execute([
        'numero_dossier' => $numero_dossier,
        'numero_cles' => $numero_cles,
        'nom_client' => $nom_client,
        'ce' => $ce,
        'css' => $css,
        'technicien' => $technicien,
        'created_by' => $created_by,
    ]);

    $dossier_id = $pdo->lastInsertId();

    // Upload des fichiers
    if (!empty($_FILES['attachments']['name'][0])) {
        $upload_dir = 'uploads/';
        foreach ($_FILES['attachments']['name'] as $key => $name) {
            $tmp_name = $_FILES['attachments']['tmp_name'][$key];
            $filename = $upload_dir . basename($name);
            if (move_uploaded_file($tmp_name, $filename)) {
                $stmt = $pdo->prepare("INSERT INTO attachments (dossier_id, filename, uploaded_by) VALUES (:dossier_id, :filename, :uploaded_by)");
                $stmt->execute([
                    'dossier_id' => $dossier_id,
                    'filename' => $filename,
                    'uploaded_by' => $created_by,
                ]);
            }
        }
    }

    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Créer un dossier</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/qrcode.min.js"></script>
    <script src="js/qr-scanner.min.js"></script>
</head>
<body>
    <div class="header">
        <a href="index.php">Accueil</a>
    </div>
    <h2>Créer un nouveau dossier</h2>
    <form method="post" action="create_dossier.php" enctype="multipart/form-data">
        <label for="numero_dossier">Numéro de Dossier:</label>
        <input type="number" id="numero_dossier" name="numero_dossier" required>
        <label for="numero_cles">Numéro de Clés:</label>
        <input type="text" id="numero_cles" name="numero_cles" required>
        <label for="nom_client">Nom du Client:</label>
        <input type="text" id="nom_client" name="nom_client" required>
        <label for="ce">CE:</label>
        <textarea id="ce" name="ce"></textarea>
        <label for="css">CSS:</label>
        <textarea id="css" name="css"></textarea>
        <label for="technicien">Technicien:</label>
        <textarea id="technicien" name="technicien"></textarea>
        <label for="attachments">Pièces jointes:</label>
        <input type="file" id="attachments" name="attachments[]" multiple>
        <button type="submit">Créer</button>
    </form>
    <div>
        <button id="generate-qr">Générer QR Code</button>
        <div id="qrcode"></div>
    </div>
    <div>
        <video id="qr-video" width="300" height="300" style="display:none;"></video>
    </div>
    <script>
        // Générer QR Code
        document.getElementById('generate-qr').addEventListener('click', function() {
            const qrcode = new QRCode(document.getElementById('qrcode'), {
                text: window.location.href,
                width: 128,
                height: 128,
            });
        });

        // Scanner QR Code
        const video = document.getElementById('qr-video');
        const qrScanner = new QrScanner(video, result => {
            window.location.href = result;
            qrScanner.stop();
        });

        document.getElementById('attachments').addEventListener('change', function() {
            video.style.display = 'block';
            qrScanner.start();
        });
    </script>
</body>
</html>