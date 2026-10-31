<?php
require_once 'database.php';

$message = "";
$messageType = "";

$db = Database::getConnexion();

// --- TRAITEMENT DE L'INSERTION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->beginTransaction();

        // 1. Prochain ID pour Livre
        $stmtMaxLivre = $db->query("SELECT MAX(IDLIVRE) as max_id FROM livre");
        $rowLivre = $stmtMaxLivre->fetch();
        $idLivre = ($rowLivre['max_id'] ?? 0) + 1;

        // 2. Insertion Livre
        $titre = $_POST['titre'];
        $date_pub = $_POST['date_publication'];
        $code_unique = $_POST['code_unique'];

        $stmtLivre = $db->prepare("INSERT INTO livre (IDLIVRE, TITRE, DATEPUBLICATION, CODEUNIQUE) VALUES (?, ?, ?, ?)");
        $stmtLivre->execute([$idLivre, $titre, $date_pub, $code_unique]);

        // 3. Insertion Auteurs et liens
        if (isset($_POST['auteurs']) && is_array($_POST['auteurs'])) {
            foreach ($_POST['auteurs'] as $auteurData) {
                $nom = $auteurData['nom'];
                $nationalite = $auteurData['nationalite'];

                if (empty($nom)) continue;

                $stmtMaxAuteur = $db->query("SELECT MAX(IDAUTEUR) as max_id FROM auteur");
                $rowAuteur = $stmtMaxAuteur->fetch();
                $idAuteur = ($rowAuteur['max_id'] ?? 0) + 1;

                $stmtAuteur = $db->prepare("INSERT INTO auteur (IDAUTEUR, NOM, NATIONALITE) VALUES (?, ?, ?)");
                $stmtAuteur->execute([$idAuteur, $nom, $nationalite]);

                $stmtEcrire = $db->prepare("INSERT INTO ecrire (IDAUTEUR, IDLIVRE) VALUES (?, ?)");
                $stmtEcrire->execute([$idAuteur, $idLivre]);
            }
        }

        $db->commit();

        // Récupérer le nombre total de livres
        $stmtCount = $db->query("SELECT COUNT(*) as total FROM livre");
        $totalLivres = $stmtCount->fetch()['total'];

        $message = "Le livre et ses auteurs ont été enregistrés avec succès ! (Total de livres : " . $totalLivres . ")";
        $messageType = "success";
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Bibliothèque - Enregistrement</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; color: #333; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .auteur-block { background: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 10px; position: relative; }
        .btn { display: inline-block; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; color: #fff; text-decoration: none; }
        .btn-primary { background: #2c3e50; }
        .btn-add { background: #2ecc71; margin-bottom: 15px; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Styles pour le tableau */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; color: #2c3e50; }
        tr:hover { background-color: #f9f9f9; }
        .no-data { text-align: center; padding: 20px; color: #7f8c8d; }
    </style>
</head>
<body>

<div class="container">
    <h1>📚 Système de Gestion de Bibliothèque</h1>
    
    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- SECTION FORMULAIRE -->
    <form action="" method="POST">
        <h2>➕ Enregistrer un nouveau livre</h2>
        <section>
            <div class="form-group">
                <label for="titre">Titre du livre</label>
                <input type="text" id="titre" name="titre" required placeholder="Ex: L'Étranger">
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="date_publication">Année/Date de Publication</label>
                    <input type="text" id="date_publication" name="date_publication" placeholder="Ex: 1942">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="code_unique">Code Unique (ISBN)</label>
                    <input type="text" id="code_unique" name="code_unique" placeholder="Ex: ISBN-12345">
                </div>
            </div>
        </section>

        <h3>Auteur(s)</h3>
        <div id="auteurs-container">
            <div class="auteur-block">
                <div class="form-group">
                    <label>Nom de l'auteur</label>
                    <input type="text" name="auteurs[0][nom]" required placeholder="Nom complet">
                </div>
                <div class="form-group">
                    <label>Nationalité</label>
                    <input type="text" name="auteurs[0][nationalite]" placeholder="Ex: Algérienne/Française">
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-add" onclick="addAuteur()">➕ Ajouter un autre auteur</button>

        <div style="text-align: center;">
            <input type="submit" class="btn btn-primary" value="💾 Enregistrer et Actualiser la liste" style="width: 100%; font-size: 1.1em; font-weight: bold;">
        </div>
    </form>

    <br><br>

    <!-- SECTION AFFICHAGE DE LA LISTE -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>📋 Liste de tous les livres enregistrés</h2>
        <span style="background: #2c3e50; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">
            Total : <?php echo count($livres); ?> livre(s)
        </span>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Publication</th>
                <th>Code Unique</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($livres) > 0): ?>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($livre['IDLIVRE']); ?></strong></td>
                        <td><?php echo htmlspecialchars($livre['TITRE']); ?></td>
                        <td><em><?php echo htmlspecialchars($livre['AUTEURS'] ?: 'Aucun auteur'); ?></em></td>
                        <td><?php echo htmlspecialchars($livre['DATEPUBLICATION']); ?></td>
                        <td><code><?php echo htmlspecialchars($livre['CODEUNIQUE']); ?></code></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-data">Aucun livre enregistré dans la base de données.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    let auteurCount = 1;
    function addAuteur() {
        const container = document.getElementById('auteurs-container');
        const div = document.createElement('div');
        div.className = 'auteur-block';
        div.innerHTML = `
            <div class="form-group">
                <label>Nom de l'auteur</label>
                <input type="text" name="auteurs[${auteurCount}][nom]" required placeholder="Nom complet">
            </div>
            <div class="form-group">
                <label>Nationalité</label>
                <input type="text" name="auteurs[${auteurCount}][nationalite]" placeholder="Ex: Française">
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="color: #e74c3c; border: none; background: none; cursor: pointer; position: absolute; top: 10px; right: 10px; font-weight: bold;">✖ Supprimer</button>
        `;
        container.appendChild(div);
        auteurCount++;
    }
</script>

</body>
</html>
