<?php
session_start();
$filename = 'sandwiches.json'; // Chemin vers le fichier JSON

// Inclure la connexion à la base de données
include 'db.php';

// Vérifier si l'utilisateur est connecté
$user_name = "Utilisateur"; // Nom par défaut
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Requête pour récupérer le login de l'utilisateur
    $sql = "SELECT login FROM utilisateur WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_name = $row['login'];
    }

    $stmt->close();
}

// Vérifier si le fichier JSON existe et peut être lu
if (file_exists($filename)) {
    $json_content = file_get_contents($filename);
    $sandwiches = json_decode($json_content, true);

    // Vérifier si le contenu JSON a été correctement décodé
    if ($sandwiches === null) {
        $sandwiches = [];
        error_log("Erreur : Impossible de décoder le fichier JSON.");
    }
} else {
    $sandwiches = [];
    error_log("Erreur : Le fichier sandwiches.json est introuvable.");
}
?>

<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandwich</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <header>

        <div class="header-container d-flex justify-content-between align-items-center">
            <h1 class="m-0">Sandwich</h1>

            <div class="user-links">

                <h2>Hey <?= htmlspecialchars($user_name) ?>! 👋</h2>
                <a href="design-preview.html" class="btn btn-info" style="background:#2A6A5A; border-color:#2A6A5A;">Design preview</a>
                <?php
                if (isset($_SESSION['user_id'])) {
                    // Afficher les boutons pour les utilisateurs connectés
                    echo '<a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Se Déconnecter</a>';
                    echo '<a href="commandes.php" class="btn btn-success"><i class="fas fa-list"></i> Gérer les Commandes</a>';
                } else {
                    // Afficher les boutons pour les utilisateurs non connectés
                    echo '<a href="login.php" class="btn btn-primary">Se Connecter</a>';
                    echo '<a href="signup.php" class="btn btn-secondary">S\'inscrire</a>';
                }
                ?>

            </div>
        </div>

    </header>

    <div class="container mt-3">
        <?php if (isset($_SESSION['message']) && $_SESSION['message'] !== ''): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <section class="popular-picks mt-4">
            <h3>Popular Picks</h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <?php if (!empty($sandwiches)): ?>
                    <?php foreach ($sandwiches as $name => $details): ?>
                        <div class="card">
                            <img src="<?= $details['image'] ?>" class="card-img-top" alt="<?= $name ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= ucfirst($name) ?></h5>
                                <p class="card-text">Prix: <?= $details['price'] ?> €</p>
                                <a href="sandwich_detail/sandwich.php?name=<?= urlencode($name) ?>" class="btn btn-primary">Add +</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun sandwich disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>

    <footer class="text-center mt-5 py-3" style="background-color: #007bff; color: white;">
        <p>Copyright&copy; Tous droits réservés à Cyril Libouton et Alexy Viatour</p>
    </footer>
</body>
</html>
