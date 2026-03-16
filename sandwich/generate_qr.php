<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php'; // Inclure l'autoloader de Composer

use Endroid\QrCode\QrCode;
	use Endroid\QrCode\Writer\PngWriter;

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour générer un QR code.");
}

try {
    // Connexion à la base de données
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer la commande du jour pour l'utilisateur connecté
    $today = date('Y-m-d');
    $sql = "SELECT nom, jour FROM commandes LIMIT 1";// WHERE id_utilisateur = :user_id AND jour = :today
    $stmt = $db_connection->prepare($sql);
    $stmt->execute([
        //':user_id' => $_SESSION['user_id'],
        //':today' => $today
    ]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
  //  var_dump($commande); // Debugging: afficher la commande récupérée
   // exit();

    if (!$commande) {
        die("Aucune commande trouvée pour aujourd'hui.");
    }

    // Contenu du QR code
    $qrContent = "Commande du jour : " . $commande['nom'] . " (Jour : " . $commande['jour'] . ")";

    // Générer le QR code
    $image_code = '';

	
		if($qrContent !== ''){
			$temporary_directory = 'temp/';
			$file_name = md5(uniqid()) . '.png';
			$file_path = $temporary_directory . $file_name;

			$qr_Code = new QrCode(trim($qrContent));
			$qr_Code->setSize(300);

			$writer = new PngWriter();
			$result = $writer->write($qr_Code);

			$result->saveToFile($file_path);
			$image_code = '<div class="text-center"><img src="'.$file_path.'" /></div>';
		}
	
    exit;
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur lors de la génération du QR code : " . $e->getMessage());
}