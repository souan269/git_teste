<?php
// config/database.php - VERSION PDO (Recommandée)

// =============================================
// 1. INFORMATIONS DE CONNEXION
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'bibliotheque');
define('DB_USER', 'root');
define('DB_PASS', 'root');  // MAMP utilise 'root' comme mot de passe

// =============================================
// 2. CLASSE DATABASE (SINGLETON)
// =============================================
class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Constructeur privé - crée la connexion à la base de données
     */
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir l'instance unique de la connexion (Singleton)
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Obtenir la connexion PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Préparer une requête SQL
     */
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    /**
     * Récupérer le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Démarrer une transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
}
?>