<?php
// connexion.php

// Démarrer la session pour vérifier si l'utilisateur est déjà connecté
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Rediriger vers le tableau de bord approprié
    if ($_SESSION['user_role'] == 1) {
        header('Location: admin/dashboard.php');
    } elseif ($_SESSION['user_role'] == 2) {
        header('Location: bibliothecaire/dashboard.php');
    } else {
        header('Location: usager/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Bibliothèque Universitaire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #1a3c5a 0%, #2c5a7a 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 400px;
            max-width: 90%;
        }
        .login-container .logo {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }
        .login-container h1 {
            text-align: center;
            color: #1a3c5a;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .login-container .subtitle {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #1a3c5a;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #1a3c5a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2c5a7a;
        }
        .error {
            background: #fde8e8;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
            border-left: 4px solid #c62828;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
            border-left: 4px solid #2e7d32;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }
        .demo-info {
            background: #f0f2f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 13px;
        }
        .demo-info strong {
            color: #1a3c5a;
        }
        .demo-info table {
            width: 100%;
            font-size: 12px;
            margin-top: 8px;
            border-collapse: collapse;
        }
        .demo-info td {
            padding: 4px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .demo-info td:last-child {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">📚</div>
        <h1>Bibliothèque Universitaire</h1>
        <p class="subtitle">Système de Gestion de Bibliothèque</p>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php 
                    switch($_GET['error']) {
                        case 'identifiants': echo '❌ Email ou mot de passe incorrect.'; break;
                        case 'compte_inactif': echo '⚠️ Votre compte est suspendu ou bloqué. Veuillez contacter la bibliothèque.'; break;
                        case 'session_expiree': echo '⏰ Votre session a expiré. Veuillez vous reconnecter.'; break;
                        case 'acces_refuse': echo '⛔ Vous n\'avez pas les droits pour accéder à cette page.'; break;
                        default: echo '❌ Une erreur est survenue. Veuillez réessayer.';
                    }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'deconnexion'): ?>
            <div class="success">✅ Vous avez été déconnecté avec succès.</div>
        <?php endif; ?>
        
        <form action="traitement_connexion.php" method="POST">
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" placeholder="votre.email@universite.com" required>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">🔒 Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit" class="btn">🔐 Se connecter</button>
        </form>
        
        <!--<div class="demo-info">
            <strong>🔑 Comptes de test</strong>
            <table>
                <tr>
                    <td><strong>Administrateur</strong></td>
                    <td>admin@bibliotheque.com</td>
                    <td>mot de passe</td>
                </tr>
                <tr>
                    <td><strong>Bibliothécaire</strong></td>
                    <td>biblio@bibliotheque.com</td>
                    <td>mot de passe</td>
                </tr>
                <tr>
                    <td><strong>Usager</strong></td>
                    <td>jean.dupont@universite.com</td>
                    <td>mot de passe</td>
                </tr>
            </table>
        </div>-->
        
        <div class="footer">
            Projet de Soutenance • Version 1.0
        </div>
    </div>
</body>
</html>