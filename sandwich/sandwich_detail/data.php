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

$sandwich = trim($_POST['sandwich'] ?? '');
$crudites = ($_POST['crudites'] ?? '') === 'avec' ? 'avec' : 'sans';
$jours = $_POST['jours'] ?? [];

if ($sandwich === '') {
    $_SESSION['message'] = 'Veuillez sélectionner un sandwich.';
    header('Location: ../index.php');
    exit();
}

if (empty($jours)) {
    $_SESSION['message'] = 'Veuillez sélectionner au moins un jour de livraison.';
    header('Location: ../sandwich_detail/sandwich.php?name=' . urlencode(strtolower($sandwich)));
    exit();
}

try {
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich;charset=utf8', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $insertSql = "INSERT INTO commandes (jour, id_utilisateur, crudites, nom, date_de_commande) VALUES (:jour, :user_id, :crudites, :nom, :date_de_commande)";
    $stmt = $db_connection->prepare($insertSql);

    $inserted = 0;
    $skipped = 0;

    foreach ($jours as $jour_val) {
        if (!str_contains($jour_val, '|')) {
            $skipped++;
            continue;
        }

        list($jour, $date_formatee) = explode('|', $jour_val);
        $date_obj = DateTime::createFromFormat('d/m/Y', trim($date_formatee));
        if (!$date_obj) {
            $skipped++;
            continue;
        }

        $date_de_commande = $date_obj->format('Y-m-d');

        try {
            $stmt->execute([
                ':jour' => trim($jour),
                ':user_id' => $_SESSION['user_id'],
                ':crudites' => $crudites,
                ':nom' => $sandwich,
                ':date_de_commande' => $date_de_commande,
            ]);
            $inserted++;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') {
                error_log('Erreur insertion commande : ' . $e->getMessage());
            }
            $skipped++;
        }
    }

    $_SESSION['message'] = "Commande enregistrée : {$inserted}. Entrées ignorées : {$skipped}.";
    header('Location: ../commandes.php');
    exit();
} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données dans data.php : ' . $e->getMessage());
    $_SESSION['message'] = 'Impossible de traiter votre commande. Veuillez réessayer plus tard.';
    header('Location: ../index.php');
    exit();
}
?>
