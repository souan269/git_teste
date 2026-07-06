<?php
// includes/session.php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class SessionManager {
    
    /**
     * Connecter un utilisateur
     */
    public static function login($id_usager, $nom, $prenom, $email, $id_role, $nom_role) {
        $_SESSION['user_id'] = $id_usager;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $id_role;
        $_SESSION['user_role_nom'] = $nom_role;
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
        
        // Enregistrer l'action dans l'historique
        self::logAction($id_usager, 'CONNEXION', "Connexion de l'utilisateur $nom $prenom");
    }
    
    /**
     * Déconnecter un utilisateur
     */
    public static function logout() {
        if (self::isLoggedIn()) {
            $id_usager = $_SESSION['user_id'];
            $nom = $_SESSION['user_nom'];
            $prenom = $_SESSION['user_prenom'];
            
            self::logAction($id_usager, 'DECONNEXION', "Déconnexion de l'utilisateur $nom $prenom");
        }
        
        $_SESSION = array();
        session_destroy();
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Récupérer les informations de l'utilisateur connecté
     */
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'nom' => $_SESSION['user_nom'],
                'prenom' => $_SESSION['user_prenom'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role'],
                'role_nom' => $_SESSION['user_role_nom']
            ];
        }
        return null;
    }
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public static function hasRole($role) {
        return self::isLoggedIn() && $_SESSION['user_role'] == $role;
    }
    
    /**
     * Exiger que l'utilisateur soit connecté
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ../connexion.php?error=session_expiree');
            exit();
        }
    }
    
    /**
     * Exiger un rôle spécifique
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!self::hasRole($role) && $_SESSION['user_role'] != 1) {
            header('Location: ../acces_refuse.php');
            exit();
        }
    }
    
    /**
     * Vérifier la session (timeout)
     */
    public static function checkSessionTimeout($timeout = 3600) {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            header('Location: ../connexion.php?error=session_expiree');
            exit();
        }
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Enregistrer une action dans l'historique
     */
    private static function logAction($id_usager, $action, $description) {
        try {
            require_once __DIR__ . '/../config/database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "INSERT INTO historique_actions (id_usager, action, description, date_action) 
                    VALUES (:id_usager, :action, :description, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_usager' => $id_usager,
                ':action' => $action,
                ':description' => $description
            ]);
        } catch (Exception $e) {
            // On ne bloque pas le système si l'historique échoue
        }
    }
}
?>