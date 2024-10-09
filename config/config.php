<?php

class Config
{
    private static $pdo = null;

    // Define database connection parameters as constants or static variables
    private static $host = 'localhost';
    private static $port = 3308; // Specify the port
    private static $dbname = 'location'; // Ensure this matches your actual database name
    private static $username = 'root';
    private static $password = '';

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . self::$host . ';port=' . self::$port . ';dbname=' . self::$dbname,
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );

                // Check the current database
                $currentDB = self::$pdo->query('SELECT DATABASE()')->fetchColumn();
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Usage
$db = Config::getConnexion();

