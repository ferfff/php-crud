<?php 
/*
* Class singleton to connect DB
*/
class Database {
	protected static $instance;
    private static $dsn = 'mysql:host=localhost;dbname=zipdev';
    private static $username = 'user';
    private static $password = 'pass';

    private function __construct() {
        try {
            self::$instance = new PDO(self::$dsn, self::$username, self::$password);
        } catch (PDOException $e) {
            echo "MySql Connection Error: " . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            new Database();
        }

        return self::$instance;
    }
}
