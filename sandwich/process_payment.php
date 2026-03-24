<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: commandes.php');
    exit();
}

$order_id = intval($_POST['order_id'] ?? 0);
$payment_method = trim($_POST['payment_method'] ?? '');

if (!$order_id || !$payment_method) {
    $_SESSION['message'] = 'Données de paiement invalides.';
    header('Location: paiement.php?order_id=' . $order_id);
    exit();
}

try {
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier que la commande appartient à l'utilisateur
    $sql = "SELECT * FROM commandes WHERE id_commande = :order_id AND id_utilisateur = :user_id";
    $stmt = $db_connection->prepare($sql);
    $stmt->execute([':order_id' => $order_id, ':user_id' => $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        $_SESSION['message'] = 'Commande introuvable.';
        header('Location: commandes.php');
        exit();
    }

    // Charger les prix depuis le JSON
    $sandwiches = json_decode(file_get_contents('sandwiches.json'), true) ?? [];
    $amount = $sandwiches[strtolower($order['nom'])]['price'] ?? 5.00;
    $transaction_id = null;

    if ($payment_method === 'card') {
        // Traiter le paiement par carte (simulation)
        $card_number = trim($_POST['card_number'] ?? '');
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');

        if (!$card_number || !$expiry_date || !$cvv) {
            $_SESSION['message'] = 'Détails de carte incomplets.';
            header('Location: paiement.php?order_id=' . $order_id);
            exit();
        }

        // Simulation de paiement réussi
        $payment_success = true;
    } elseif ($payment_method === 'paypal') {
        // Simulation PayPal
        $payment_success = true;
    } elseif ($payment_method === 'cash') {
        // Paiement à la livraison
        $payment_success = true;
    } elseif ($payment_method === 'bank') {
        // Virement bancaire
        $payment_success = true;
    } else {
        $_SESSION['message'] = 'Méthode de paiement invalide.';
        header('Location: paiement.php?order_id=' . $order_id);
        exit();
    }

    if ($payment_success) {
        // Insérer la transaction
        $insert_transaction = "INSERT INTO transaction (heure, montant, jour_, id_utilisateur) VALUES (:heure, :montant, :jour, :user_id)";
        $stmt_trans = $db_connection->prepare($insert_transaction);
        $stmt_trans->execute([
            ':heure' => date('H:i:s'),
            ':montant' => $amount,
            ':jour' => date('Y-m-d'),
            ':user_id' => $_SESSION['user_id']
        ]);
        $transaction_id = $db_connection->lastInsertId();

        // Lier la transaction à la commande via facturation
        $insert_facturation = "INSERT INTO facturation (id_commande, id_transaction) VALUES (:order_id, :transaction_id)";
        $stmt_fact = $db_connection->prepare($insert_facturation);
        $stmt_fact->execute([
            ':order_id' => $order_id,
            ':transaction_id' => $transaction_id
        ]);

        $_SESSION['message'] = 'Paiement effectué avec succès !';
    } else {
        $_SESSION['message'] = 'Échec du paiement. Veuillez réessayer.';
    }

    header('Location: commandes.php');
    exit();
} catch (PDOException $e) {
    error_log('Erreur de paiement : ' . $e->getMessage());
    $_SESSION['message'] = 'Erreur lors du traitement du paiement.';
    header('Location: commandes.php');
    exit();
}
?>