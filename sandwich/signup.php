<?php
session_start();

try {
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification des entrées utilisateur
        $username = trim($_POST['username']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = trim($_POST['password']);

        if (empty($username) || !$email || empty($password)) {
            $_SESSION['message'] = "Veuillez remplir tous les champs correctement.";
        } else {
            // Vérifier si l'email existe déjà
            $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
            $stmt = $db_connection->prepare($sql);
            $stmt->execute([':email' => $email]);
            $emailExists = $stmt->fetchColumn();

            if ($emailExists) {
                $_SESSION['message'] = "Cet email est déjà utilisé.";
            } else {
                // Hacher le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur dans la base de données
                $sql = "INSERT INTO utilisateur (login, email, password) VALUES (:username, :email, :password)";
                $stmt = $db_connection->prepare($sql);
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);

                $_SESSION['message'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                header("Location: login.php");
                exit();
            }
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="container">
        <h2>Inscription</h2>

        <!-- Affichage des messages d'erreur -->
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Supprimer le message après l'affichage
        }
        ?>

        <form action="signup.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
    </div>
</body>
</html>
