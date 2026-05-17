<?php

class ForumService extends Service
{
    public function createTopic(int $usuarioId, string $titulo): int
    {
        $titulo = trim($titulo);
        if ($titulo === '') {
            throw new ValidationException(['titulo' => 'El título es obligatorio']);
        }

        $sql = 'INSERT INTO temas (titulo, usuario_id) VALUES (?, ?)';
        $this->execute($sql, [$titulo, $usuarioId]);

        return (int) $this->lastInsertId();
    }

    public function replyTopic(int $temaId, int $usuarioId, string $texto): int
    {
        $texto = trim($texto);
        if ($temaId <= 0) {
            throw new ValidationException(['tema_id' => 'El tema es inválido']);
        }

        if ($texto === '') {
            throw new ValidationException(['texto' => 'El texto es obligatorio']);
        }

        $sql = 'INSERT INTO respuestas (tema_id, usuario_id, texto) VALUES (?, ?, ?)';
        $this->execute($sql, [$temaId, $usuarioId, $texto]);

        return (int) $this->lastInsertId();
    }

    public function getAllTopics(): array
    {
        $sql = "SELECT
                    t.*,
                    u.nombre AS autor,
                    (SELECT COUNT(*) FROM respuestas r WHERE r.tema_id = t.id) AS total_respuestas
                FROM temas t
                LEFT JOIN usuarios u ON u.id = t.usuario_id
                ORDER BY t.fecha DESC";
        return $this->fetchAll($sql);
    }

    public function getTopicById(int $temaId): ?array
    {
        $sql = "SELECT t.*, u.nombre AS autor
                FROM temas t
                LEFT JOIN usuarios u ON u.id = t.usuario_id
                WHERE t.id = ?";
        return $this->fetch($sql, [$temaId]);
    }

    public function getRepliesByTopic(int $temaId): array
    {
        $sql = "SELECT r.*, u.nombre AS autor
                FROM respuestas r
                LEFT JOIN usuarios u ON u.id = r.usuario_id
                WHERE r.tema_id = ?
                ORDER BY r.id ASC";
        return $this->fetchAll($sql, [$temaId]);
    }
}
