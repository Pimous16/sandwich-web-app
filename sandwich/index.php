<?php
session_start();
$filename = 'sandwiches.json'; // Chemin vers le fichier JSON

// Lire le fichier JSON et le décoder en un tableau associatif PHP
$sandwiches = json_decode(file_get_contents($filename), true);

$days = [
    'Monday' => 'Lundi',
    'Tuesday' => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday' => 'Jeudi',
    'Friday' => 'Vendredi',
    'Saturday' => 'Samedi',
    'Sunday' => 'Dimanche',
];


$date = date('l');
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

    <div class="container mt-5">

        <h2 class="text-center mb-4">Choisissez votre sandwich</h2>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php foreach ($sandwiches as $name => $details): ?>
                <a href="sandwich_detail/sandwich.php?name=<?= urlencode($name) ?>" class="case"><?= ucfirst($name) ?></a>
            <?php endforeach; ?>

        </div><br>

        <!--<div class="mt-5 text-center">
            <a href="probleme.php" class="btn btn-warning btn-lg report-problem">
                <span>Signaler un problème</span>
            </a>
        </div>-->

        <!--<div class="mt-3 text-center">
            <img src="temp/<?= $days[$date] ?>_<?= $_SESSION['user_id'] ?>.png" alt="QR Code de la commande du jour">
        </div>-->
    </div>

    <footer class="text-center mt-5 py-3" style="background-color: var(--primary-color); color: white;">
        <p>Copyright&copy; Tous droits réservés Cyril du CEPES 2025</p>
    </footer>
</body>
</html>
