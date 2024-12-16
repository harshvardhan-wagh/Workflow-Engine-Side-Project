<?php

namespace workFlowManager;

use PDO;
use PDOException;

class Database {
    private $host = '127.0.0.1';
    private $db   = 'workflow_db';
    private $user = 'root';
    private $pass = 'Vssc123!@#';
    private $charset = 'utf8mb4';

    public $pdo;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
}
