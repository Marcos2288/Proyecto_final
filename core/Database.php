<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
        $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value === false ? null : $value;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }
}
