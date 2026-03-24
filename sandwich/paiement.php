<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
try {
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Charger les images des sandwichs depuis le fichier JSON (pour l'affichage)
$sandwiches = [];
$jsonPath = __DIR__ . '/sandwiches.json';
if (file_exists($jsonPath)) {
    $sandwiches = json_decode(file_get_contents($jsonPath), true) ?? [];
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header('Location: commandes.php');
    exit();
}

// Récupérer les détails de la commande
$sql = "SELECT * FROM commandes WHERE id_commande = :order_id AND id_utilisateur = :user_id";
$stmt = $db_connection->prepare($sql);
$stmt->execute([':order_id' => $order_id, ':user_id' => $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    header('Location: commandes.php');
    exit();
}

$price = $sandwiches[strtolower($order['nom'])]['price'] ?? 5.00;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement de la Commande</title>
    <link rel="stylesheet" href="commande.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['message']) && $_SESSION['message'] !== ''): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <h2 class="text-center">Paiement de la Commande</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Détails de la Commande</h5>
                <p><strong>Jour :</strong> <?= htmlspecialchars($order['jour']) ?></p>
                <p><strong>Sandwich :</strong> <?= htmlspecialchars($order['nom']) ?></p>
                <p><strong>Crudités :</strong> <?= htmlspecialchars($order['crudites'] ?? 'Aucune') ?></p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($price) ?>€</p>
            </div>
        </div>
        <form action="process_payment.php" method="post">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
            <div class="mb-3">
                <label for="payment_method" class="form-label">Choisissez un moyen de paiement :</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="">Sélectionnez...</option>
                    <option value="card">Carte de Crédit</option>
                    <option value="paypal">PayPal</option>
                    <option value="cash">Espèces à la livraison</option>
                    <option value="bank">Virement Bancaire</option>
                </select>
            </div>
            <div id="card_details" style="display: none;">
                <h5>Détails de la Carte</h5>
                <div class="mb-3">
                    <label for="card_number" class="form-label">Numéro de Carte</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expiry_date" class="form-label">Date d'Expiration</label>
                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Payer Maintenant</button>
        </form>
        <div class="bottom">
            <a href="commandes.php">
                <span class="btn">
                    <span class="arrow">←</span> Retour aux Commandes
                </span>
            </a>
        </div>
    </div>
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            var cardDetails = document.getElementById('card_details');
            if (this.value === 'card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        });
    </script>
</body>
</html>