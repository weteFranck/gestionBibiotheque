<?php
require_once '/config/database.php';

$message = "";
$messageType = "";
$db = Database::getConnexion();

// --- RÉCUPÉRATION DES LECTEURS ET DES LIVRES POUR LES SÉLECTEURS ---
$lecteurs = $db->query("SELECT * FROM lecteur ORDER BY NOM ASC")->fetchAll();
$livres = $db->query("SELECT * FROM livre ORDER BY TITRE ASC")->fetchAll();

// --- TRAITEMENT DE L'EMPRUNT ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enregistrer_emprunt'])) {
    try {
        $db->beginTransaction();
        
        $idLecteur = $_POST['id_lecteur'];
        $dateEmprunt = $_POST['date_emprunt'];
        $duree = $_POST['duree']; // Nombre de jours
        
        if (isset($_POST['livres']) && is_array($_POST['livres'])) {
            foreach ($_POST['livres'] as $idLivre) {
                if (empty($idLivre)) continue;
                
                // Calcul du prochain ID (PK: NBRFOISEMP selon le schéma SQL fourni)
                $stmtMax = $db->query("SELECT MAX(NBRFOISEMP) as max_id FROM emprunt");
                $nextId = ($stmtMax->fetch()['max_id'] ?? 0) + 1;
                
                // Insertion dans la table emprunt
                $stmt = $db->prepare("INSERT INTO emprunt (NBRFOISEMP, IDLIVRE, IDLECTEUR, DATEDONNEE, DATERETOUR, QUANTITE) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nextId, $idLivre, $idLecteur, $dateEmprunt, $duree, 1]);
            }
            
            $db->commit();
            $message = "Emprunt(s) enregistré(s) avec succès !";
            $messageType = "success";
        } else {
            throw new Exception("Veuillez sélectionner au moins un livre.");
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $message = "Erreur : " . $e->getMessage();
        $messageType = "error";
    }
}

function addBookSelect() {
     const container = document.getElementById('books-container');
     const div = document.createElement('div');
        div.className = 'book-item';
        
        let options = '<option value="">-- Choisir un livre --</option>';
        <?php foreach ($livres as $livre): ?>
            options += '<option value="<?php echo $livre['IDLIVRE']; ?>"><?php echo addslashes(htmlspecialchars($livre['TITRE'])); ?></option>';
        <?php endforeach; ?>
        
        div.innerHTML = `
            <select name="livres[]" required>${options}</select>
            <button type="button" onclick="this.parentElement.remove()" style="color:red; border:none; background:none; cursor:pointer; font-weight:bold;">✖</button>
        `;
        container.appendChild(div);
}
?>


