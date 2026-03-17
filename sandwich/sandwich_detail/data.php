<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');
$db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sandwich = trim($_POST['sandwich'] ?? '');
$crudites = ($_POST['crudites'] ?? '') === 'avec' ? 'avec' : 'sans';
$jours = $_POST['jours'] ?? [];

$insertSql = "INSERT INTO commandes (jour, id_utilisateur, crudites, nom, date_de_commande) \
              VALUES (:jour, :user_id, :crudites, :nom, :date_de_commande)";
$stmt = $db_connection->prepare($insertSql);

foreach ($jours as $jour_val) {
    if (!str_contains($jour_val, '|')) {
        continue;
    }

    list($jour, $date_formatee) = explode('|', $jour_val);
    $date_obj = DateTime::createFromFormat('d/m/Y', trim($date_formatee));
    if (!$date_obj) {
        continue;
    }

    $date_de_commande = $date_obj->format('Y-m-d');

    try {
        $stmt->execute([
            ':jour' => $jour,
            ':user_id' => $_SESSION['user_id'],
            ':crudites' => $crudites,
            ':nom' => $sandwich,
            ':date_de_commande' => $date_de_commande,
        ]);
    } catch (PDOException $e) {
        // Ignore duplicate unique key errors (commande déjà existante)
        if ($e->getCode() !== '23000') {
            error_log('Erreur insertion commande : ' . $e->getMessage());
        }
    }
}

header('Location: ../index.php');
exit();
?>
