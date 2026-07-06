<?php
// admin/gestion_livres.php - Gestion complète des livres

require_once '../includes/session.php';

if (!SessionManager::hasRole(1) && !SessionManager::hasRole(2)) {
    header('Location: ../acces_refuse.php');
    exit();
}

$user = SessionManager::getCurrentUser();

require_once '../config/database.php';
$db = Database::getInstance()->getConnection();

// =============================================
// TRAITEMENT DES ACTIONS
// =============================================
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Suppression
if ($action === 'supprimer') {
    $id_livre = (int)$_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM livres WHERE id_livre = :id");
        $stmt->execute([':id' => $id_livre]);
        header('Location: gestion_livres.php?success=supprime');
        exit();
    } catch (PDOException $e) {
        header('Location: gestion_livres.php?error=suppression');
        exit();
    }
}

// Ajout
if ($action === 'ajouter' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titre = trim($_POST['titre'] ?? '');
        $auteur = trim($_POST['auteur'] ?? '');
        $isbn = trim($_POST['isbn'] ?? '');
        $editeur = trim($_POST['editeur'] ?? '');
        $annee_publication = !empty($_POST['annee_publication']) ? (int)$_POST['annee_publication'] : null;
        $edition = trim($_POST['edition'] ?? '');
        $nombre_pages = !empty($_POST['nombre_pages']) ? (int)$_POST['nombre_pages'] : null;
        $id_categorie = !empty($_POST['id_categorie']) ? (int)$_POST['id_categorie'] : null;
        $resume = trim($_POST['resume'] ?? '');
        $mot_cles = trim($_POST['mot_cles'] ?? '');
        $code_barres = trim($_POST['code_barres'] ?? '');
        $cote = trim($_POST['cote'] ?? '');
        $emplacement = trim($_POST['emplacement'] ?? '');
        $prix_achat = !empty($_POST['prix_achat']) ? (float)$_POST['prix_achat'] : null;
        $nombre_exemplaires = !empty($_POST['nombre_exemplaires']) ? (int)$_POST['nombre_exemplaires'] : 1;

        if (empty($titre) || empty($auteur)) {
            header('Location: gestion_livres.php?error=champs_manquants&action=ajouter');
            exit();
        }

        $db->beginTransaction();

        $sql = "INSERT INTO livres (isbn, titre, auteur, editeur, annee_publication, edition, 
                nombre_pages, id_categorie, resume, mot_cles, date_ajout) 
                VALUES (:isbn, :titre, :auteur, :editeur, :annee, :edition, 
                :pages, :categorie, :resume, :mots, CURDATE())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':isbn' => $isbn,
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':editeur' => $editeur,
            ':annee' => $annee_publication,
            ':edition' => $edition,
            ':pages' => $nombre_pages,
            ':categorie' => $id_categorie,
            ':resume' => $resume,
            ':mots' => $mot_cles
        ]);
        $id_livre = $db->lastInsertId();

        for ($i = 0; $i < $nombre_exemplaires; $i++) {
            $code = ($i == 0) ? $code_barres : $code_barres . '-' . ($i + 1);
            $sql = "INSERT INTO exemplaires (id_livre, code_barres, cote, emplacement, statut, date_acquisition, prix_achat) 
                    VALUES (:id_livre, :code_barres, :cote, :emplacement, 'disponible', CURDATE(), :prix)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_livre' => $id_livre,
                ':code_barres' => $code,
                ':cote' => $cote,
                ':emplacement' => $emplacement,
                ':prix' => $prix_achat
            ]);
        }

        $db->commit();
        header('Location: gestion_livres.php?success=ajoute');
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        header('Location: gestion_livres.php?error=base_donnees&action=ajouter');
        exit();
    }
}

// Modification
if ($action === 'modifier' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_livre = (int)$_POST['id_livre'];
        $isbn = trim($_POST['isbn'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $auteur = trim($_POST['auteur'] ?? '');
        $editeur = trim($_POST['editeur'] ?? '');
        $annee_publication = !empty($_POST['annee_publication']) ? (int)$_POST['annee_publication'] : null;
        $edition = trim($_POST['edition'] ?? '');
        $nombre_pages = !empty($_POST['nombre_pages']) ? (int)$_POST['nombre_pages'] : null;
        $id_categorie = !empty($_POST['id_categorie']) ? (int)$_POST['id_categorie'] : null;
        $resume = trim($_POST['resume'] ?? '');
        $mot_cles = trim($_POST['mot_cles'] ?? '');

        if (empty($titre) || empty($auteur)) {
            header('Location: gestion_livres.php?error=champs_manquants&modifier=' . $id_livre);
            exit();
        }

        $sql = "UPDATE livres SET 
                isbn = :isbn,
                titre = :titre,
                auteur = :auteur,
                editeur = :editeur,
                annee_publication = :annee,
                edition = :edition,
                nombre_pages = :pages,
                id_categorie = :categorie,
                resume = :resume,
                mot_cles = :mots
                WHERE id_livre = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':isbn' => $isbn,
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':editeur' => $editeur,
            ':annee' => $annee_publication,
            ':edition' => $edition,
            ':pages' => $nombre_pages,
            ':categorie' => $id_categorie,
            ':resume' => $resume,
            ':mots' => $mot_cles,
            ':id' => $id_livre
        ]);

        header('Location: gestion_livres.php?success=modifie');
        exit();

    } catch (PDOException $e) {
        header('Location: gestion_livres.php?error=base_donnees&modifier=' . $id_livre);
        exit();
    }
}

// =============================================
// RÉCUPÉRATION DES DONNÉES
// =============================================
$sql = "SELECT l.*, c.nom_categorie 
        FROM livres l
        LEFT JOIN categories c ON l.id_categorie = c.id_categorie
        ORDER BY l.titre ASC";
$stmt = $db->query($sql);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_exemplaires = "SELECT id_livre, COUNT(*) as total FROM exemplaires GROUP BY id_livre";
$stmt_exemplaires = $db->query($sql_exemplaires);
$exemplaires_count = [];
while ($row = $stmt_exemplaires->fetch(PDO::FETCH_ASSOC)) {
    $exemplaires_count[$row['id_livre']] = $row['total'];
}

$sql_disponibles = "SELECT id_livre, COUNT(*) as total FROM exemplaires WHERE statut = 'disponible' GROUP BY id_livre";
$stmt_disponibles = $db->query($sql_disponibles);
$disponibles_count = [];
while ($row = $stmt_disponibles->fetch(PDO::FETCH_ASSOC)) {
    $disponibles_count[$row['id_livre']] = $row['total'];
}

$stmt = $db->query("SELECT * FROM categories ORDER BY nom_categorie");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =============================================
// DÉTERMINER SI LE FORMULAIRE DOIT ÊTRE AFFICHÉ
// =============================================
$show_form = false;
$livre_modification = null;
$form_action = 'ajouter';

if (isset($_GET['modifier']) && !empty($_GET['modifier'])) {
    $stmt = $db->prepare("SELECT * FROM livres WHERE id_livre = :id");
    $stmt->execute([':id' => (int)$_GET['modifier']]);
    $livre_modification = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($livre_modification) {
        $show_form = true;
        $form_action = 'modifier';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'ajouter' && !$show_form) {
    $show_form = true;
    $form_action = 'ajouter';
}

if (isset($_GET['error']) && isset($_GET['action']) && $_GET['action'] === 'ajouter') {
    $show_form = true;
    $form_action = 'ajouter';
}
if (isset($_GET['error']) && isset($_GET['modifier'])) {
    $show_form = true;
    $form_action = 'modifier';
    $stmt = $db->prepare("SELECT * FROM livres WHERE id_livre = :id");
    $stmt->execute([':id' => (int)$_GET['modifier']]);
    $livre_modification = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des livres - BiblioCom</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; color: #37423b; margin-bottom: 5px; font-size: 14px; }
        .form-group label .required { color: #dc3545; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #eef0ef;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #5fcf80;
            box-shadow: 0 0 0 3px rgba(95, 207, 128, 0.15);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .table-actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .search-box { display: flex; gap: 10px; max-width: 350px; }
        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #eef0ef;
            border-radius: 50px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            outline: none;
            border-color: #5fcf80;
            box-shadow: 0 0 0 3px rgba(95, 207, 128, 0.15);
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .form-section {
            display: none;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef0ef;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0px 0 18px rgba(55, 66, 59, 0.08);
        }
        .form-section.visible {
            display: block;
            animation: fadeSlideIn 0.4s ease;
        }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-section .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #37423b;
            margin-bottom: 20px;
            border-bottom: 2px solid #eef0ef;
            padding-bottom: 12px;
        }
        .form-section .form-title i {
            margin-right: 10px;
            color: #5fcf80;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .btn-annuler {
            background: #6c757d;
            color: #fff;
        }
        .btn-annuler:hover {
            background: #5a6268;
            color: #fff;
        }

        /* =============================================
           MODAL DE SUPPRESSION PERSONNALISÉ
           ============================================= */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-overlay.active {
            display: flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-box {
            background: #fff;
            border-radius: 10px;
            max-width: 450px;
            width: 90%;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-box .modal-icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }
        .modal-box .modal-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #37423b;
            margin-bottom: 10px;
        }
        .modal-box .modal-message {
            text-align: center;
            color: #555;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .modal-box .modal-message strong {
            color: #dc3545;
        }
        .modal-box .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .modal-box .modal-actions .btn-modal {
            padding: 10px 30px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .modal-box .modal-actions .btn-modal-confirm {
            background: #dc3545;
            color: #fff;
        }
        .modal-box .modal-actions .btn-modal-confirm:hover {
            background: #c82333;
        }
        .modal-box .modal-actions .btn-modal-cancel {
            background: #6c757d;
            color: #fff;
        }
        .modal-box .modal-actions .btn-modal-cancel:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>

    <!-- ======= Header ======= -->
    <header class="admin-header">
        <div class="container">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 3h2v18H9zm11.71 17.23-3.8-8.15-3.81-8.16-.9.42-.91.43 3.8 8.15 3.81 8.16.9-.42zM6 3h2v18H6zM3 3h2v18H3z"></path>
                </svg>
                <span>Biblio</span>Com
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                <span class="user-role"><?= htmlspecialchars($user['role_nom']) ?></span>
                <div class="avatar"><?= strtoupper(substr($user['prenom'], 0, 1)) . strtoupper(substr($user['nom'], 0, 1)) ?></div>
                <a href="../deconnexion.php" class="btn-logout">🔒 Déconnexion</a>
            </div>
        </div>
    </header>

    <!-- ======= Navigation ======= -->
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="bx bx-home"></i> Dashboard</a></li>
            <li><a href="gestion_livres.php" class="active"><i class="bx bx-book"></i> Livres</a></li>
            <li><a href="#"><i class="bx bx-user"></i> Usagers</a></li>
            <li><a href="#"><i class="bx bx-list-check"></i> Prêts</a></li>
        </ul>
    </nav>

    <!-- ======= Contenu ======= -->
    <div class="admin-container">

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    switch($_GET['success']) {
                        case 'ajoute': echo '✅ Livre ajouté avec succès !'; break;
                        case 'modifie': echo '✅ Livre modifié avec succès !'; break;
                        case 'supprime': echo '✅ Livre supprimé avec succès !'; break;
                        default: echo '✅ Opération réussie !';
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    switch($_GET['error']) {
                        case 'champs_manquants': echo '❌ Le titre et l\'auteur sont obligatoires.'; break;
                        case 'suppression': echo '❌ Impossible de supprimer ce livre.'; break;
                        default: echo '❌ Une erreur est survenue.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <!-- ============================================= -->
        <!-- FORMULAIRE -->
        <!-- ============================================= -->
        <div id="formSection" class="form-section <?= $show_form ? 'visible' : '' ?>">
            <div class="form-title">
                <i class="bx <?= $form_action === 'modifier' ? 'bx-edit-alt' : 'bx-plus-circle' ?>"></i>
                <?= $form_action === 'modifier' ? 'Modifier le livre' : 'Ajouter un nouveau livre' ?>
            </div>

            <form action="gestion_livres.php" method="POST">
                <input type="hidden" name="action" value="<?= $form_action ?>">
                <?php if ($form_action === 'modifier' && $livre_modification): ?>
                    <input type="hidden" name="id_livre" value="<?= $livre_modification['id_livre'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($livre_modification['isbn'] ?? '') ?>" placeholder="978-2-10-083134-6">
                    </div>
                    <div class="form-group">
                        <label for="annee_publication">Année de publication</label>
                        <input type="number" id="annee_publication" name="annee_publication" value="<?= $livre_modification['annee_publication'] ?? '' ?>" placeholder="2024">
                    </div>
                </div>

                <div class="form-group">
                    <label for="titre">Titre <span class="required">*</span></label>
                    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($livre_modification['titre'] ?? '') ?>" placeholder="Titre du livre" required>
                </div>

                <div class="form-group">
                    <label for="auteur">Auteur <span class="required">*</span></label>
                    <input type="text" id="auteur" name="auteur" value="<?= htmlspecialchars($livre_modification['auteur'] ?? '') ?>" placeholder="Nom de l'auteur" required>
                </div>

                <div class="form-group">
                    <label for="editeur">Éditeur</label>
                    <input type="text" id="editeur" name="editeur" value="<?= htmlspecialchars($livre_modification['editeur'] ?? '') ?>" placeholder="Maison d'édition">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edition">Édition</label>
                        <input type="text" id="edition" name="edition" value="<?= htmlspecialchars($livre_modification['edition'] ?? '') ?>" placeholder="2ème édition">
                    </div>
                    <div class="form-group">
                        <label for="nombre_pages">Nombre de pages</label>
                        <input type="number" id="nombre_pages" name="nombre_pages" value="<?= $livre_modification['nombre_pages'] ?? '' ?>" placeholder="450">
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_categorie">Catégorie</label>
                    <select id="id_categorie" name="id_categorie">
                        <option value="">-- Sélectionner une catégorie --</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id_categorie'] ?>" <?= ($livre_modification && $categorie['id_categorie'] == $livre_modification['id_categorie']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categorie['nom_categorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="resume">Résumé</label>
                    <textarea id="resume" name="resume" placeholder="Brève description du livre..."><?= htmlspecialchars($livre_modification['resume'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="mot_cles">Mots-clés</label>
                    <input type="text" id="mot_cles" name="mot_cles" value="<?= htmlspecialchars($livre_modification['mot_cles'] ?? '') ?>" placeholder="programmation, algorithmes, structures de données">
                </div>

                <?php if ($form_action === 'ajouter'): ?>
                    <hr style="margin: 20px 0; border-color: #eef0ef;">
                    <h4 style="color: #37423b; margin-bottom: 15px;">📚 Informations sur l'exemplaire</h4>

                    <div class="form-group">
                        <label for="code_barres">Code-barres</label>
                        <input type="text" id="code_barres" name="code_barres" placeholder="BU-001">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cote">Cote</label>
                            <input type="text" id="cote" name="cote" placeholder="INF 004.6">
                        </div>
                        <div class="form-group">
                            <label for="emplacement">Emplacement</label>
                            <input type="text" id="emplacement" name="emplacement" placeholder="Rayon A1, Étagère 1">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prix_achat">Prix d'achat (FCFA)</label>
                            <input type="number" id="prix_achat" name="prix_achat" placeholder="25000">
                        </div>
                        <div class="form-group">
                            <label for="nombre_exemplaires">Nombre d'exemplaires</label>
                            <input type="number" id="nombre_exemplaires" name="nombre_exemplaires" value="1" min="1" max="20">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn <?= $form_action === 'modifier' ? 'btn-primary' : 'btn-success' ?>">
                        <?= $form_action === 'modifier' ? '💾 Enregistrer les modifications' : '💾 Ajouter le livre' ?>
                    </button>
                    <a href="gestion_livres.php" class="btn btn-annuler">❌ Annuler</a>
                </div>
            </form>
        </div>

        <!-- ============================================= -->
        <!-- LISTE DES LIVRES -->
        <!-- ============================================= -->
        <div class="card">
            <div class="card-title">📚 Liste des livres</div>

            <div class="action-bar">
                <div class="left">
                    <a href="gestion_livres.php?action=ajouter" class="btn btn-success" id="btnAjouterLivre">
                        <i class="bx bx-plus-circle"></i> Ajouter un livre
                    </a>
                </div>
                <div class="right">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="🔍 Rechercher un livre..." onkeyup="rechercher()">
                    </div>
                </div>
            </div>

            <?php if (count($livres) > 0): ?>
                <div class="table-responsive">
                    <table class="table" id="livresTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Catégorie</th>
                                <th>Ex.</th>
                                <th>Dispo</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($livres as $l): 
                                $total = $exemplaires_count[$l['id_livre']] ?? 0;
                                $disponibles = $disponibles_count[$l['id_livre']] ?? 0;
                            ?>
                            <tr>
                                <td><?= $l['id_livre'] ?></td>
                                <td><strong><?= htmlspecialchars($l['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($l['auteur']) ?></td>
                                <td><?= htmlspecialchars($l['nom_categorie'] ?? '-') ?></td>
                                <td><?= $total ?></td>
                                <td>
                                    <?php if ($disponibles > 0): ?>
                                        <span class="badge badge-success"><?= $disponibles ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="gestion_livres.php?modifier=<?= $l['id_livre'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="openModal(<?= $l['id_livre'] ?>, '<?= addslashes($l['titre']) ?>')" class="btn btn-danger btn-sm" title="Supprimer">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <span class="icon">📚</span>
                    <h3>Aucun livre dans la bibliothèque</h3>
                    <p>Cliquez sur le bouton "Ajouter un livre" pour commencer.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- ======= Footer ======= -->
    <div class="admin-footer">
        &copy; Copyright <strong>BiblioCom</strong>. All Rights Reserved
    </div>

    <!-- ============================================= -->
    <!-- MODAL DE SUPPRESSION PERSONNALISÉ -->
    <!-- ============================================= -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">🗑️</div>
            <div class="modal-title">Suppression</div>
            <div class="modal-message" id="modalMessage">
                Voulez-vous vraiment supprimer le livre :<br>
                <strong id="modalBookTitle">"Nom du livre"</strong> ?
                <br><br>
                <span style="color: #dc3545; font-size: 13px;">⚠️ Cette action est irréversible.</span>
            </div>
            <div class="modal-actions">
                <button class="btn-modal btn-modal-confirm" id="modalConfirmBtn">🗑️ Supprimer</button>
                <button class="btn-modal btn-modal-cancel" onclick="closeModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script>
        // Variable pour stocker l'ID du livre à supprimer
        var livreASupprimer = null;

        // Ouvrir le modal avec le titre du livre
        function openModal(id, titre) {
            console.log("Modal ouvert pour le livre ID: " + id + " - Titre: " + titre);
            livreASupprimer = id;
            document.getElementById('modalBookTitle').textContent = '"' + titre + '"';
            document.getElementById('deleteModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Fermer le modal
        function closeModal() {
            document.getElementById('deleteModal').classList.remove('active');
            document.body.style.overflow = '';
            livreASupprimer = null;
        }

        // Confirmer la suppression
        document.getElementById('modalConfirmBtn').addEventListener('click', function() {
            if (livreASupprimer !== null) {
                window.location.href = 'gestion_livres.php?action=supprimer&id=' + livreASupprimer;
            }
        });

        // Fermer le modal si on clique en dehors
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Fermer le modal avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Fonction de recherche
        function rechercher() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let table = document.getElementById('livresTable');
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        }

        // Animation pour défiler vers le formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const formSection = document.getElementById('formSection');
            if (formSection && formSection.classList.contains('visible')) {
                setTimeout(function() {
                    formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 200);
            }
        });
    </script>

</body>
</html>