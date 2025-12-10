<?php
class Database {
    private $pdo = null;
    public function __construct(){
        $cfg = require __DIR__ . '/../config.php';
        $db = $cfg['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
        try {
            $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            echo 'DB Connection failed: ' . $e->getMessage();
            exit;
        }
    }
    public function pdo(){ return $this->pdo; }
}
?>