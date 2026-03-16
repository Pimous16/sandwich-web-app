<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    $db_connection = new PDO('mysql:host=localhost;dbname=sandwich', 'root', '');

    if (isset($_POST)) var_dump($_POST);
    echo "<hr>";
    if (isset($_GET)) var_dump($_GET);

    echo "Votre {$_POST['sandwich']} est demandé <b>{$_POST['crudites']} crudités.</b>";

    echo "<ul>Les jours demandés :";
    foreach ($_POST['jours'] as $jour_val) {
        // Séparer le jour (ex: "Vendredi") et la date (ex: "11/04/2025")
        list($jour, $date_formatee) = explode('|', $jour_val);

        // Convertir au format YYYY-MM-DD pour la base de données
        $date_obj = DateTime::createFromFormat('d/m/Y', trim($date_formatee));
        $date_de_commande = $date_obj->format('Y-m-d');

        // Définir crudités proprement
        $crudites = ($_POST['crudites'] === 'avec') ? 'avec' : 'sans';

        try {
            $db_connection->query("INSERT INTO commandes (jour, id_utilisateur, crudites, nom, date_de_commande) 
                                   VALUES ('$jour', {$_SESSION['user_id']}, '$crudites', '{$_POST['sandwich']}', '$date_de_commande')");
            echo "<p>Commande pour le $jour enregistrée avec la date de commande $date_de_commande.</p>";
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                echo "<p>Vous avez déjà commandé pour le $jour.</p>";
            } else {
                echo "<p>Erreur lors de l'insertion de la commande pour le $jour : " . $e->getMessage() . "</p>";
            }
        }
    }
    echo "</ul>";

    // Redirection
    header("Location: ../index.php");
    exit();
}
?>
