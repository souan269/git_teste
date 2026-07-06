<?php
// traitement_connexion.php

// Inclure les fichiers nécessaires
require_once 'config/database.php';
require_once 'includes/session.php';

// Vérifier que les données sont envoyées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit();
}

// Récupérer les données du formulaire
$email = trim($_POST['email'] ?? '');
$mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

// Vérifier que les champs ne sont pas vides
if (empty($email) || empty($mot_de_passe)) {
    header('Location: connexion.php?error=identifiants');
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer l'utilisateur avec son rôle
    $sql = "SELECT u.*, r.nom_role 
            FROM utilisateurs u
            JOIN roles r ON u.id_role = r.id_role
            WHERE u.email = :email";
    $stmt = $db->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérifier si l'utilisateur existe
    if (!$user) {
        header('Location: connexion.php?error=identifiants');
        exit();
    }
    
    // Vérifier le mot de passe
    if (!password_verify($mot_de_passe, $user['mot_de_passe'])) {
        header('Location: connexion.php?error=identifiants');
        exit();
    }
    
    // Vérifier si le compte est actif
    if ($user['statut'] != 'actif') {
        // Vérifier si la suspension est terminée
        if ($user['suspension_jusquau'] && $user['suspension_jusquau'] < date('Y-m-d')) {
            // Réactiver automatiquement le compte
            $sql = "UPDATE utilisateurs SET statut = 'actif', suspension_jusquau = NULL WHERE id_usager = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $user['id_usager']]);
        } else {
            header('Location: connexion.php?error=compte_inactif');
            exit();
        }
    }
    
    // Connecter l'utilisateur
    SessionManager::login(
        $user['id_usager'],
        $user['nom'],
        $user['prenom'],
        $user['email'],
        $user['id_role'],
        $user['nom_role']
    );
    
    // Rediriger vers le tableau de bord approprié
    if ($user['id_role'] == 1) {
        header('Location: admin/dashboard.php');
    } elseif ($user['id_role'] == 2) {
        header('Location: bibliothecaire/dashboard.php');
    } else {
        header('Location: usager/dashboard.php');
    }
    exit();
    
} catch (PDOException $e) {
    // En cas d'erreur, rediriger vers la page de connexion
    header('Location: connexion.php?error=systeme');
    exit();
}
?>