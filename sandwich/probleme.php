<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/PHPMailer/PHPMailer-master/src/Exception.php';

// Initialisation du message de feedback
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['critique'])) {
    $critique = htmlspecialchars(strip_tags($_POST['critique']), ENT_QUOTES, 'UTF-8'); // Sécurisation des données
    $mail = new PHPMailer(true);
    try {
        // Configuration SMTP sécurisée avec des variables d'environnement
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Serveur SMTP de Gmail
        $mail->SMTPAuth = true; // Authentification nécessaire
        $mail->Username = getenv('MAIL_USERNAME'); // Utiliser une variable d'environnement pour l'email
        $mail->Password = getenv('MAIL_PASSWORD'); // Utiliser une variable d'environnement pour le mot de passe
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sécurisation de la connexion
        $mail->Port = 587; // Port pour STARTTLS
        
        // Destinataires
        $mail->setFrom('no-reply@example.com', 'Support');
        $mail->addAddress('cyrilbouton@gmail.com'); // E-mail du support
        
        // Contenu du mail
        $mail->isHTML(true);
        $mail->Subject = 'Problème Commande';
        $mail->Body = "Un utilisateur a signalé un problème : <br><br>" . nl2br($critique);
        
        // Envoi du mail
        if ($mail->send()) {
            // Message de succès
            $feedback = "<div class='alert alert-success text-center'>Votre message a été envoyé avec succès.</div>";
        }
    } catch (Exception $e) {
        // Log des erreurs pour le débogage
        error_log('Erreur d\'envoi de mail : ' . $mail->ErrorInfo);
        $feedback = "<div class='alert alert-danger text-center'>Une erreur est survenue, veuillez réessayer plus tard.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un problème</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Signaler un problème</h1>
        <?php if (!empty($feedback)): ?>
            <div class="mt-3"><?php echo $feedback; ?></div>
        <?php endif; ?>
        <form method="post" action="probleme.php" class="mt-4">
            <div class="mb-3">
                <label for="critique" class="form-label">Décrivez votre problème :</label>
                <textarea id="critique" name="critique" class="form-control" rows="5" placeholder="Expliquez votre problème ici..." required></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Envoyer</button>
        </form>
        <div class="mt-3 text-center">
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
