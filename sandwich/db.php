<?php
$servername = "localhost";
$username = "root"; // Utilisateur par défaut sur XAMPP
$password = ""; // Mot de passe vide par défaut
$dbname = "sandwich"; // Le nom de la base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
