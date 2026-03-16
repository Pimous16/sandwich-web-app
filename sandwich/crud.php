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

switch($_GET['action']){
    case 'delete':{
        $db_connection->query("DELETE FROM commandes WHERE id_commande = {$_GET['id_commande']} AND id_utilisateur = {$_SESSION['user_id']}");
        header('Location: commandes.php');
        break;
    }
    
    case 'creer':{
        echo "suppression";
        break;
    }
    
    case 'modifier': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $id_commande = intval($_POST['id_commande']);
            $nouveau_sandwich = htmlspecialchars($_POST['sandwich']);
            $crudites = htmlspecialchars($_POST['crudites']);

            // Mettre à jour la commande dans la base de données
            $sql = "UPDATE commandes SET nom = :sandwich, crudites = :crudites WHERE id_commande = :id_commande AND id_utilisateur = :user_id";
            $stmt = $db_connection->prepare($sql);
            $stmt->execute([
                ':sandwich' => $nouveau_sandwich,
                ':crudites' => $crudites,
                ':id_commande' => $id_commande,
                ':user_id' => $_SESSION['user_id']
            ]);

            // Rediriger vers la page des commandes
            header('Location: commandes.php');
            exit();
        } else {
            // Charger les sandwichs depuis le fichier JSON
            $sandwiches = json_decode(file_get_contents('sandwiches.json'), true);

            // Récupérer les informations de la commande à modifier
            $id_commande = intval($_GET['id_commande']);
            $sql = "SELECT nom, crudites FROM commandes WHERE id_commande = :id_commande AND id_utilisateur = :user_id";
            $stmt = $db_connection->prepare($sql);
            $stmt->execute([
                ':id_commande' => $id_commande,
                ':user_id' => $_SESSION['user_id']
            ]);
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($commande) {
                echo '<form method="POST" action="crud.php?action=modifier">';
                echo '<input type="hidden" name="id_commande" value="' . htmlspecialchars($id_commande) . '">';
                echo '<label for="sandwich">Sandwich :</label>';
                echo '<select id="sandwich" name="sandwich" required>';
                foreach ($sandwiches as $name => $details) {
                    $selected = ($commande['nom'] === ucfirst($name)) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars(ucfirst($name)) . '" ' . $selected . '>' . ucfirst($name) . '</option>';
                }
                echo '</select>';
                echo '<br>';
                echo '<label for="crudites">Crudités :</label>';
                echo '<input type="radio" id="avec" name="crudites" value="avec" ' . ($commande['crudites'] === 'avec' ? 'checked' : '') . '> Avec';
                echo '<input type="radio" id="sans" name="crudites" value="sans" ' . ($commande['crudites'] === 'sans' ? 'checked' : '') . '> Sans';
                echo '<br>';
                echo '<button type="submit">Modifier</button>';
                echo '</form>';
            } else {
                echo '<p>Commande introuvable.</p>';
            }
        }
        break;
    }
    
    case 'afficher_une_commande':{
        echo "suppression";
        break;
    }
    
    case 'afficher_les_commandes':{
        echo "afficher commande admin";
        break;
    }
}