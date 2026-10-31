<?php
require_once '/config/database.php';

$message = "";
$messageType = "";
$db = Database::getConnexion();

// --- GESTION DE LA SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $db->beginTransaction();
        
        // Supprimer d'abord les liens dans la table 'ecrire' (contrainte FK)
        $stmtDeleteEcrire = $db->prepare("DELETE FROM ecrire WHERE IDLIVRE = ?");
        $stmtDeleteEcrire->execute([$id]);
        
        // Supprimer ensuite le livre
        $stmtDeleteLivre = $db->prepare("DELETE FROM livre WHERE IDLIVRE = ?");
        $stmtDeleteLivre->execute([$id]);
        
        $db->commit();
        $message = "Livre supprimé avec succès !";
        $messageType = "success";
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $message = "Erreur lors de la suppression : " . $e->getMessage();
        $messageType = "error";
    }
}

// --- PRÉPARATION DE LA MODIFICATION (RÉCUPÉRATION DES DONNÉES) ---
$editMode = false;
$bookToEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $editMode = true;
    $stmtFetch = $db->prepare("SELECT * FROM livre WHERE IDLIVRE = ?");
    $stmtFetch->execute([$_GET['id']]);
    $bookToEdit = $stmtFetch->fetch();
}

// --- TRAITEMENT DE L'INSERTION / MODIFICATION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->beginTransaction();

        $titre = $_POST['titre'];
        $date_pub = $_POST['date_publication'];
        $code_unique = $_POST['code_unique'];

        if (isset($_POST['id_livre']) && !empty($_POST['id_livre'])) {
            // --- MODE MODIFICATION ---
            $idLivre = $_POST['id_livre'];
            $stmtUpdate = $db->prepare("UPDATE livre SET TITRE = ?, DATEPUBLICATION = ?, CODEUNIQUE = ? WHERE IDLIVRE = ?");
            $stmtUpdate->execute([$titre, $date_pub, $code_unique, $idLivre]);
            
            // Pour simplifier, on ne gère que la mise à jour des infos de base du livre ici.
            // La gestion complète des auteurs en modification nécessiterait plus de logique.
            
            $message = "Livre mis à jour avec succès !";
        } else {
            // --- MODE INSERTION ---
            $stmtMaxLivre = $db->query("SELECT MAX(IDLIVRE) as max_id FROM livre");
            $idLivre = ($stmtMaxLivre->fetch()['max_id'] ?? 0) + 1;

            $stmtLivre = $db->prepare("INSERT INTO livre (IDLIVRE, TITRE, DATEPUBLICATION, CODEUNIQUE) VALUES (?, ?, ?, ?)");
            $stmtLivre->execute([$idLivre, $titre, $date_pub, $code_unique]);

            // Insertion Auteurs (seulement en mode insertion pour cet exemple)
            if (isset($_POST['auteurs']) && is_array($_POST['auteurs'])) {
                foreach ($_POST['auteurs'] as $auteurData) {
                    if (empty($auteurData['nom'])) continue;
                    
                    $stmtMaxAuteur = $db->query("SELECT MAX(IDAUTEUR) as max_id FROM auteur");
                    $idAuteur = ($stmtMaxAuteur->fetch()['max_id'] ?? 0) + 1;

                    $stmtAuteur = $db->prepare("INSERT INTO auteur (IDAUTEUR, NOM, NATIONALITE) VALUES (?, ?, ?)");
                    $stmtAuteur->execute([$idAuteur, $auteurData['nom'], $auteurData['nationalite']]);

                    $stmtEcrire = $db->prepare("INSERT INTO ecrire (IDAUTEUR, IDLIVRE) VALUES (?, ?)");
                    $stmtEcrire->execute([$idAuteur, $idLivre]);
                }
            }
            $message = "Le livre a été enregistré avec succès !";
        }

        $db->commit();
        $messageType = "success";
        $editMode = false; // Sortir du mode édition après succès
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $message = "Erreur : " . $e->getMessage();
        $messageType = "error";
    }
}

// RÉCUPÉRATION DE LA LISTE DES LIVRES 
$query = "SELECT l.IDLIVRE, l.TITRE, l.DATEPUBLICATION, l.CODEUNIQUE, 
                 GROUP_CONCAT(a.NOM SEPARATOR ', ') as AUTEURS
          FROM livre l
          LEFT JOIN ecrire e ON l.IDLIVRE = e.IDLIVRE
          LEFT JOIN auteur a ON e.IDAUTEUR = a.IDAUTEUR
          GROUP BY l.IDLIVRE
          ORDER BY l.IDLIVRE DESC";
$livres = $db->query($query)->fetchAll();

let auteurCount = 1;
    function addAuteur() {
        const container = document.getElementById('auteurs-container');
        const div = document.createElement('div');
        div.className = 'auteur-block';
        div.innerHTML = `
            <div class="form-group"><label>Nom</label><input type="text" name="auteurs[${auteurCount}][nom]" required></div>
            <div class="form-group"><label>Nationalité</label><input type="text" name="auteurs[${auteurCount}][nationalite]"></div>
            <button type="button" onclick="this.parentElement.remove()" style="color:red; border:none; background:none; cursor:pointer; position:absolute; top:10px; right:10px;">✖</button>
        `;
        container.appendChild(div);
        auteurCount++;
    }
?>


