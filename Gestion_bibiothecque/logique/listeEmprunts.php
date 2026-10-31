<?php
require_once '/config/database.php';

$db = Database::getConnexion();

// --- RÉCUPÉRATION DES STATISTIQUES D'EMPRUNT PAR LIVRE ---
$queryStats = "SELECT l.TITRE, COUNT(e.IDLIVRE) as TOTAL_EMPRUNTS
               FROM livre l
               JOIN emprunt e ON l.IDLIVRE = e.IDLIVRE
               GROUP BY l.IDLIVRE
               ORDER BY TOTAL_EMPRUNTS DESC";
$statsLivres = $db->query($queryStats)->fetchAll();

// --- RÉCUPÉRATION DE LA LISTE DÉTAILLÉE DES EMPRUNTS ---
$queryListe = "SELECT e.*, l.TITRE, le.NOM as NOM_LECTEUR
               FROM emprunt e
               JOIN livre l ON e.IDLIVRE = l.IDLIVRE
               JOIN lecteur le ON e.IDLECTEUR = le.IDLECTEUR
               ORDER BY e.DATEDONNEE DESC";
$emprunts = $db->query($queryListe)->fetchAll();
?>

