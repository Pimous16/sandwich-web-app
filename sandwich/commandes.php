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
// Récupérer les commandes depuis la base de données (avec la date pour la gestion action)
$commandes = [];
$sql = "SELECT id_commande, jour, date_de_commande, nom, crudites FROM commandes WHERE id_utilisateur = :user_id";
$stmt = $db_connection->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger les images des sandwichs depuis le fichier JSON (pour l'affichage)
$sandwiches = [];
$jsonPath = __DIR__ . '/sandwiches.json';
if (file_exists($jsonPath)) {
    $sandwiches = json_decode(file_get_contents($jsonPath), true) ?? [];
}

// Vérifier si une commande est modifiable
function estModifiable($jour) {
    $heureActuelle = new DateTime();
    $dateCommande = DateTime::createFromFormat('Y-m-d', $jour);
    if (!$dateCommande) {
        return false;
    }

    // Calcul de la date limite de modification : veille du jour de livraison à 16h
    $deadline = (clone $dateCommande)->setTime(16, 0)->modify('-1 day');

    // Action possible tant qu'on est avant la date limite
    return $heureActuelle < $deadline;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="commande.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['message']) && $_SESSION['message'] !== ''): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <h2 class="text-center">Gestion des Commandes de la Semaine</h2>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Jour</th>
                    <th>Sandwich</th>
                    <th>Crudités</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): 
                    $nom = $commande['nom'] ?? 'Inconnu';
                    $key = strtolower($nom);
                    $imageUrl = $sandwiches[$key]['image'] ?? 'https://via.placeholder.com/120?text=Sandwich';
                    $isEditable = estModifiable($commande['date_de_commande']);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['jour']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($nom) ?>" class="sandwich-thumb">
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($nom) ?></div>
                                    <?php if (!empty($sandwiches[$key]['price'])): ?>
                                        <small class="text-muted">Prix: <?= htmlspecialchars($sandwiches[$key]['price']) ?>€</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($commande['crudites'] ?? '') ?></td>
                        <td>
                            <?php if ($isEditable): ?>
                                <a href="crud.php?action=modifier&id_commande=<?= $commande['id_commande'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                <a href="crud.php?action=delete&id_commande=<?= $commande['id_commande'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette commande ?');">Supprimer</a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Verrouillée</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="bottom">
            <a href="index.php">
                <span class="btn">
                    <span class="arrow">←</span> Retour
                </span>
            </a>
        </div>
    </div>
</body>
</html>