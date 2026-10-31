<?php

class Database {
    private static $pdo = null;

    private static $host = 'localhost';
    private static $db_name = 'gestionBibiotheque';
    private static $user_name = 'root';
    private static $password = '';

    public static function getConnexion() {
        if (self::$pdo === null) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                    self::$user_name,
                    self::$password,
                    $options
                );
            } catch (PDOException $e) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Erreur de connexion : " . $e->getMessage()]);
                exit();
            }
        }
        return self::$pdo;
    }
}
?>