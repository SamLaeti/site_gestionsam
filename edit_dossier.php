<?php
require 'db.php';
require 'functions.php';
redirectIfNotLoggedIn();

$dossier_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM dossiers WHERE id = :id");
$stmt->execute(['id' => $dossier_id]);
$dossier = $stmt->fetch();

if (!$dossier) {
    die('Dossier non trouvé');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $numero_dossier = $_POST['numero_dossier'];
    $numero_cles = $_POST['numero_cles'];
    $nom_client = $_POST['nom_client'];
    $ce = $_POST['ce'];
    $css = $_POST['css'];
    $technicien = $_POST['technicien'];

    $stmt = $pdo->prepare("UPDATE dossiers SET numero_dossier = :numero_dossier, numero_cles = :numero_cles, nom_client = :nom_client, ce = :ce, css = :css, technicien = :technicien WHERE id = :id");
    $stmt->execute([
        'numero_dossier' => $numero_dossier,
        'numero_cles' => $numero_cles,
        'nom_client' => $nom_client,
        'ce' => $ce,
        'css' => $css,
        'technicien' => $technicien,
        'id' => $dossier_id,
    ]);

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
                    'uploaded_by' => $_SESSION['user_id'],
                ]);
            }
        }
    }

    header('Location: index.php?update=success');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $currentDateTime = date('Y-m-d H:i:s');
    $ce = $dossier['ce'] . "\n" . $currentDateTime . " - véhicule terminé";
    
    $stmt = $pdo->prepare("UPDATE dossiers SET ce = :ce WHERE id = :id");
    $stmt->execute([
        'ce' => $ce,
        'id' => $dossier_id,
    ]);

    header('Location: index.php?update=success');
    exit();
}

// Récupérer les pièces jointes
$stmt = $pdo->prepare("SELECT * FROM attachments WHERE dossier_id = :dossier_id");
$stmt->execute(['dossier_id' => $dossier_id]);
$attachments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer le dossier</title>
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
        <h2>Éditer le dossier n°<?php echo $dossier['numero_dossier']; ?></h2>
        <form method="post" action="edit_dossier.php?id=<?php echo $dossier['id']; ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="numero_dossier" class="form-label">Numéro de Dossier:</label>
                <input type="number" class="form-control" id="numero_dossier" name="numero_dossier" value="<?php echo $dossier['numero_dossier']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="numero_cles" class="form-label">Numéro de Clés:</label>
                <input type="text" class="form-control" id="numero_cles" name="numero_cles" value="<?php echo $dossier['numero_cles']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nom_client" class="form-label">Nom du Client:</label>
                <input type="text" class="form-control" id="nom_client" name="nom_client" value="<?php echo $dossier['nom_client']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="ce" class="form-label">CE:</label>
                <textarea class="form-control" id="ce" name="ce"><?php echo $dossier['ce']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="css" class="form-label">CSS:</label>
                <textarea class="form-control" id="css" name="css"><?php echo $dossier['css']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="technicien" class="form-label">Technicien:</label>
                <textarea class="form-control" id="technicien" name="technicien"><?php echo $dossier['technicien']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="attachments" class="form-label">Pièces jointes:</label>
                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" name="update">Mettre à jour</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='index.php'">Annuler</button>
            </div>
        </form>
        <form method="post" action="edit_dossier.php?id=<?php echo $dossier['id']; ?>">
            <button type="submit" class="btn btn-success mt-3" name="mark_complete">Signaler que le véhicule est terminé</button>
        </form>
        <h3 class="mt-4">Pièces jointes existantes</h3>
        <ul class="list-group">
            <?php foreach ($attachments as $attachment): ?>
                <li class="list-group-item"><a href="<?php echo $attachment['filename']; ?>" target="_blank"><?php echo basename($attachment['filename']); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-4">
            <button id="generate-qr" class="btn btn-info">Générer QR Code</button>
            <div id="qrcode" class="mt-3"></div>
        </div>
        <div class="mt-4">
            <video id="qr-video" width="300" height="300" style="display:none;"></video>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/js/qrcode.min.js"></script>
        <script src="/js/qr-scanner.min.js" type="module"></script>
        <script type="module">
            import QrScanner from '/js/qr-scanner.min.js';

            // Générer QR Code
            document.getElementById('generate-qr').addEventListener('click', function() {
                const qrcode = new QRCode(document.getElementById('qrcode'), {
                    text: window.location.href,
                    width: 128,
                    height: 128,
                });
            });

            // Scanner QR Code
            document.addEventListener("DOMContentLoaded", function() {
                const video = document.getElementById('qr-video');
                const qrScanner = new QrScanner(video, result => {
                    window.location.href = result;
                    qrScanner.stop();
                });

                document.getElementById('attachments').addEventListener('change', function() {
                    video.style.display = 'block';
                    qrScanner.start();
                });
            });
        </script>
        <script src="/js/autosize.min.js"></script>
        <script>
            // Agrandir automatiquement les zones de texte avec autosize
            document.addEventListener("DOMContentLoaded", function() {
                console.log("Script exécuté !");
                let textareas = document.querySelectorAll("#ce, #css, #technicien");
                console.log("Textareas trouvés :", textareas.length);

                if (typeof autosize === "function") {
                    console.log("autosize est bien chargé !");
                    autosize(textareas);
                    
                    textareas.forEach(t => {
                        t.addEventListener("input", () => {
                            console.log("Mise à jour de :", t.id);
                            autosize.update(t);
                        });
                    });

                } else {
                    console.error("autosize.js ne semble pas chargé !");
                }
            });
        </script>
    </div>
</body>
</html>