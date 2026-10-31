<?php
require_once 'config/database.php';

$db = Database::getConnexion();

// --- RÉCUPÉRATION DES LECTEURS ET DE LEURS EMPRUNTS ---
$query = "SELECT le.IDLECTEUR, le.NOM, le.ADRESSE, le.TELEPHONE, 
                 GROUP_CONCAT(l.TITRE SEPARATOR ' | ') as LIVRES_EMPRUNTES,
                 COUNT(e.IDLIVRE) as NB_EMPRUNTS
          FROM lecteur le
          LEFT JOIN emprunt e ON le.IDLECTEUR = e.IDLECTEUR
          LEFT JOIN livre l ON e.IDLIVRE = l.IDLIVRE
          GROUP BY le.IDLECTEUR
          ORDER BY le.NOM ASC";
$lecteurs = $db->query($query)->fetchAll();
?>