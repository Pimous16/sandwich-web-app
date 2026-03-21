<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php'; // Inclure l'autoloader de Composer

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    die("Vous devez être connecté pour générer un QR code.");
}

try {
    // Connexion à la base de données
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich;charset=utf8', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer la commande du jour pour l'utilisateur connecté
    $today = date('Y-m-d');
    $sql = "SELECT nom, jour FROM commandes WHERE id_utilisateur = :user_id AND jour = :today LIMIT 1";
    $stmt = $db_connection->prepare($sql);
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':today' => $today
    ]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
  //  var_dump($commande); // Debugging: afficher la commande récupérée
   // exit();

    if (!$commande) {
        die("Aucune commande trouvée pour aujourd'hui.");
    }

    // Contenu du QR code
    $qrContent = "Commande du jour : " . $commande['nom'] . " (Jour : " . $commande['jour'] . ")";

    if (!is_dir(__DIR__ . '/temp')) {
        mkdir(__DIR__ . '/temp', 0755, true);
    }

    $temporary_directory = 'temp/';
    $file_name = md5(uniqid('', true)) . '.png';
    $file_path = $temporary_directory . $file_name;

    $qrCode = new QrCode($qrContent);
    $qrCode->setSize(300);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $result->saveToFile($file_path);

    $image_url = htmlspecialchars($file_path);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur lors de la génération du QR code : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Commande</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Votre QR Code de commande</h1>
        <p>Commande : <?= htmlspecialchars($commande['nom']) ?> (<?= htmlspecialchars($commande['jour']) ?>)</p>
        <img src="<?= $image_url ?>" alt="QR Code de la commande" />
        <div class="mt-3">
            <a href="commandes.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</body>
</html>