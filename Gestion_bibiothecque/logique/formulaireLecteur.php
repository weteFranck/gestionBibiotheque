<?php
require_once '/config/database.php';

$message = "";
$messageType = "";
$db = Database::getConnexion();

// --- TRAITEMENT DE L'ENREGISTREMENT ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enregistrer_lecteur'])) {
    try {
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];

        // Calcul du prochain ID
        $stmtMax = $db->query("SELECT MAX(IDLECTEUR) as max_id FROM lecteur");
        $idLecteur = ($stmtMax->fetch()['max_id'] ?? 0) + 1;

        $stmt = $db->prepare("INSERT INTO lecteur (IDLECTEUR, NOM, ADRESSE, TELEPHONE) VALUES (?, ?, ?, ?)");
        $stmt->execute([$idLecteur, $nom, $adresse, $telephone]);

        $message = "Lecteur enregistré avec succès !";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $messageType = "error";
    }
}

// RÉCUPÉRATION DE LA LISTE DES LECTEURS
$lecteurs = $db->query("SELECT * FROM lecteur ORDER BY IDLECTEUR DESC")->fetchAll();
?>