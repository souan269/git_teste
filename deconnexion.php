<?php
// deconnexion.php

require_once 'includes/session.php';

// Déconnecter l'utilisateur
SessionManager::logout();

// Rediriger vers la page de connexion
header('Location: connexion.php?success=deconnexion');
exit();
?>

