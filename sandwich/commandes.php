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
// Récupérer les commandes depuis la base de données
$commandes = [];
$sql = "SELECT id_commande, jour, nom FROM commandes WHERE id_utilisateur = :user_id";
$stmt = $db_connection->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
//echo "<pre>";
//var_dump($commandes);
//echo "</pre>";


// Vérifier si une commande est modifiable
function estModifiable($jour) {
    $heureActuelle = new DateTime();
    $dateCommande = DateTime::createFromFormat('Y-m-d', $jour);
    if (!$dateCommande) {
        return false;
    }
    $dateCommande->setTime(20, 0);
    $dateCommande->modify('-1 day');
    return $heureActuelle < $dateCommande;
}
// Sauvegarder les modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['commandes'] as $jour => $nom) {
        $sql = "UPDATE commandes SET nom = :nom WHERE jour = :jour AND id_utilisateur = :user_id";
        $stmt = $db_connection->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':jour' => $jour,
            ':user_id' => $_SESSION['user_id']
        ]);
    }
    header('Location: commandes.php');
    exit();
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
        <h2 class="text-center">Gestion des Commandes de la Semaine</h2>
        <form method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Commande</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?= htmlspecialchars($commande['jour']) ?></td>
                            <td>
                                <?php if (is_string($commande) && estModifiable($commande['jour'])): ?>
                                    <input type="text" name="commandes[<?= htmlspecialchars($jour) ?>]" value="<?= htmlspecialchars($commande) ?>" class="form-control">
                                <?php elseif (is_string($commande['nom'])): ?>
                                    <?= htmlspecialchars($commande['nom']) ?>
                                <?php else: ?>
                                    <span class="text-danger">Donnée invalide</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!estModifiable($commande['jour'])): ?>
                                    <a href="crud.php?action=delete&id_commande=<?= $commande['id_commande'] ?>"  class="btn btn-danger">Effacer</a>
                                    <a href="crud.php?action=modifier&id_commande=<?= $commande['id_commande'] ?>"  class="btn btn-success">modifier</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table><br>
            <!--<div class="text-center">
                <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
            </div>-->
        </form>

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