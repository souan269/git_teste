<?php
// admin/dashboard.php

require_once '../includes/session.php';
SessionManager::requireRole(1);

$user = SessionManager::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bibliothèque Universitaire</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #1a3c5a; }
        .user-info { background: #e8f4fd; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .btn { display: inline-block; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👑 Tableau de bord Administrateur</h1>
        
        <div class="user-info">
            <p><strong>Bienvenue, <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?> !</strong></p>
            <p>📧 <?= htmlspecialchars($user['email']) ?></p>
            <p>👤 Rôle : <?= htmlspecialchars($user['role_nom']) ?></p>
        </div>
        
        <p>✅ Vous êtes connecté en tant qu'administrateur. Vous avez accès à toutes les fonctionnalités du système.</p>
        
        <p style="margin-top: 20px;">
            <a href="../deconnexion.php" class="btn">🔒 Déconnexion</a>
        </p>
    </div>
</body>
</html>