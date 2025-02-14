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

// Récupérer les pièces jointes
$stmt = $pdo->prepare("SELECT * FROM attachments WHERE dossier_id = :dossier_id");
$stmt->execute(['dossier_id' => $dossier_id]);
$attachments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consulter un dossier</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/qrcode.min.js"></script>
    <script src="js/qr-scanner.min.js"></script>
</head>
<body>
    <div class="header">
        <a href="index.php">Accueil</a>
    </div>
    <h2>Dossier n°<?php echo $dossier['numero_dossier']; ?></h2>
    <p>Nom du Client: <?php echo $dossier['nom_client']; ?></p>
    <p>CE: <?php echo $dossier['ce']; ?></p>
    <p>CSS: <?php echo $dossier['css']; ?></p>
    <p>Technicien: <?php echo $dossier['technicien']; ?></p>
    <h3>Pièces jointes</h3>
    <ul>
        <?php foreach ($attachments as $attachment): ?>
            <li><a href="<?php echo $attachment['filename']; ?>" target="_blank"><?php echo basename($attachment['filename']); ?></a></li>
        <?php endforeach; ?>
    </ul>
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
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>