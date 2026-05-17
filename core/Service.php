<?php

abstract class Service
{
    protected Database $db;
    protected PDO $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }

    protected function fetch(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->db->fetchColumn($sql, $params);
    }

    protected function execute(string $sql, array $params = []): bool
    {
        return $this->db->execute($sql, $params);
    }

    protected function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    protected function commit(): bool
    {
        return $this->db->commit();
    }

    protected function rollback(): bool
    {
        return $this->db->rollback();
    }

    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
