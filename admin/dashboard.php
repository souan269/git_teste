<?php
// admin/dashboard.php - Tableau de bord Administrateur

require_once '../includes/session.php';
SessionManager::requireRole(1);

$user = SessionManager::getCurrentUser();

// Connexion à la base de données
require_once '../config/database.php';
$db = Database::getInstance()->getConnection();

// Statistiques
$stmt = $db->query("SELECT COUNT(*) as total FROM livres");
$total_livres = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE id_role = 3");
$total_usagers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE id_role = 2");
$total_bibliothecaires = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM prets WHERE statut = 'en_cours' OR statut = 'retard'");
$total_prets = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM prets WHERE statut = 'retard'");
$total_retards = $stmt->fetch()['total'];

// Dernières actions
$stmt = $db->query("SELECT h.*, u.nom, u.prenom 
                    FROM historique_actions h
                    LEFT JOIN utilisateurs u ON h.id_usager = u.id_usager
                    ORDER BY h.date_action DESC LIMIT 5");
$dernieres_actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - BiblioCom</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- CSS Admin -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <!-- Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
                <span class="user-role">Administrateur</span>
                <div class="avatar"><?= strtoupper(substr($user['prenom'], 0, 1)) . strtoupper(substr($user['nom'], 0, 1)) ?></div>
                <a href="../deconnexion.php" class="btn-logout">🔒 Déconnexion</a>
            </div>
        </div>
    </header>

    <!-- ======= Navigation ======= -->
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php" class="active"><i class="bx bx-home"></i> Dashboard</a></li>
            <li><a href="gestion_livres.php"><i class="bx bx-book"></i> Livres</a></li>
            <li><a href="#"><i class="bx bx-user"></i> Usagers</a></li>
            <li><a href="#"><i class="bx bx-list-check"></i> Prêts</a></li>
            <li><a href="#"><i class="bx bx-calendar"></i> Réservations</a></li>
            <li><a href="#"><i class="bx bx-stats"></i> Statistiques</a></li>
        </ul>
    </nav>

    <!-- ======= Contenu ======= -->
    <div class="admin-container">

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="icon">📚</span>
                <div class="number green"><?= $total_livres ?></div>
                <div class="label">Livres</div>
            </div>
            <div class="stat-card">
                <span class="icon">👥</span>
                <div class="number blue"><?= $total_usagers ?></div>
                <div class="label">Usagers</div>
            </div>
            <div class="stat-card">
                <span class="icon">📋</span>
                <div class="number orange"><?= $total_prets ?></div>
                <div class="label">Prêts en cours</div>
            </div>
            <div class="stat-card">
                <span class="icon">⏰</span>
                <div class="number red"><?= $total_retards ?></div>
                <div class="label">Livres en retard</div>
            </div>
            <div class="stat-card">
                <span class="icon">👤</span>
                <div class="number green"><?= $total_bibliothecaires ?></div>
                <div class="label">Bibliothécaires</div>
            </div>
        </div>

        <!-- Menu rapide -->
        <div class="card">
            <div class="card-title"><i class="bx bx-grid-alt"></i> Gestion rapide</div>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="gestion_livres.php" class="btn btn-primary"><i class="bx bx-book"></i> Gérer les livres</a>
                <a href="#" class="btn btn-success"><i class="bx bx-user-plus"></i> Ajouter un usager</a>
                <a href="#" class="btn btn-warning"><i class="bx bx-list-check"></i> Voir les prêts</a>
                <a href="#" class="btn btn-danger"><i class="bx bx-calendar"></i> Gérer les retards</a>
            </div>
        </div>

        <!-- Dernières actions -->
        <div class="card">
            <div class="card-title"><i class="bx bx-history"></i> Dernières actions</div>
            <?php if (count($dernieres_actions) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernieres_actions as $action): ?>
                            <tr>
                                <td><?= htmlspecialchars($action['prenom'] ?? 'Inconnu') . ' ' . htmlspecialchars($action['nom'] ?? '') ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($action['action']) ?></span></td>
                                <td><?= htmlspecialchars(substr($action['description'] ?? '', 0, 50)) . (strlen($action['description'] ?? '') > 50 ? '...' : '') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($action['date_action'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #888; text-align: center; padding: 20px;">Aucune action enregistrée pour le moment.</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- ======= Footer ======= -->
    <div class="admin-footer">
        &copy; Copyright <strong>BiblioCom</strong>. All Rights Reserved
    </div>

</body>
</html>