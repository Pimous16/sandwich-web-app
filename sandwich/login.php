<?php
session_start();

try {
    // Connexion à la base de données
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich;charset=utf8', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Vérification des champs vides
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['message'] = "Veuillez remplir tous les champs.";
        } else {
            // Nettoyage et validation des entrées
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = "Adresse email invalide.";
            } else {
                // Requête pour récupérer l'utilisateur
                $sql = "SELECT id_utilisateur, login, password FROM utilisateur WHERE email = :email";
                $stmt = $db_connection->prepare($sql);
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true); // Sécurisation de la session
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['username'] = $user['login'];
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['message'] = "Identifiants incorrects.";
                }
            }
        }
    }
} catch (PDOException $e) {
    // Journalisation de l'erreur dans un fichier log
    error_log("[" . date('Y-m-d H:i:s') . "] Erreur PDO : " . $e->getMessage() . "\n", 3, __DIR__ . '/errors.log');
    $_SESSION['message'] = "Une erreur est survenue. Veuillez réessayer plus tard.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>

        <!-- Affichage des messages d'erreur -->
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Supprimer le message après l'affichage
        }
        ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-label="Adresse email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" aria-label="Mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <!-- Lien vers la page d'inscription -->
        <div class="mt-3">
            <p>Pas encore de compte ? <a href="signup.php">Inscrivez-vous ici</a>.</p>
        </div>
    </div>
</body>
</html>
