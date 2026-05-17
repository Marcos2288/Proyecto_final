<?php

class RatingService extends Service
{
    public function ensureTablesExist(): void
    {
        $this->connection->exec(
            "CREATE TABLE IF NOT EXISTS ratings_contenidos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                contenido_id INT NOT NULL,
                rating DECIMAL(3,2) NOT NULL CHECK (rating >= 1 AND rating <= 10),
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY usuario_contenido (usuario_id, contenido_id),
                KEY contenido_id (contenido_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );

        $this->connection->exec(
            "CREATE TABLE IF NOT EXISTS ratings_productos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                producto_id INT NOT NULL,
                rating DECIMAL(3,2) NOT NULL CHECK (rating >= 1 AND rating <= 10),
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY usuario_producto (usuario_id, producto_id),
                KEY producto_id (producto_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    public function rateContent(int $usuarioId, int $contenidoId, float $rating): void
    {
        $this->validateRating($rating);
        $this->ensureTablesExist();

        $sql = "INSERT INTO ratings_contenidos (usuario_id, contenido_id, rating)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), fecha = CURRENT_TIMESTAMP";
        $this->execute($sql, [$usuarioId, $contenidoId, $rating]);
    }

    public function rateProduct(int $usuarioId, int $productoId, float $rating): void
    {
        $this->validateRating($rating);
        $this->ensureTablesExist();

        $sql = "INSERT INTO ratings_productos (usuario_id, producto_id, rating)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), fecha = CURRENT_TIMESTAMP";
        $this->execute($sql, [$usuarioId, $productoId, $rating]);
    }

    public function getContentRating(int $contenidoId): array
    {
        $this->ensureTablesExist();

        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM ratings_contenidos
                WHERE contenido_id = ?";

        $result = $this->fetch($sql, [$contenidoId]);
        return [
            'avg_rating' => round((float) ($result['avg_rating'] ?? 0), 1),
            'total_ratings' => (int) ($result['total_ratings'] ?? 0),
        ];
    }

    public function getProductRating(int $productoId): array
    {
        $this->ensureTablesExist();

        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM ratings_productos
                WHERE producto_id = ?";

        $result = $this->fetch($sql, [$productoId]);
        return [
            'avg_rating' => round((float) ($result['avg_rating'] ?? 0), 1),
            'total_ratings' => (int) ($result['total_ratings'] ?? 0),
        ];
    }

    public function getUserContentRating(int $usuarioId, int $contenidoId): ?float
    {
        $this->ensureTablesExist();

        $sql = 'SELECT rating FROM ratings_contenidos WHERE usuario_id = ? AND contenido_id = ?';
        $value = $this->fetchColumn($sql, [$usuarioId, $contenidoId]);
        return $value !== null ? (float) $value : null;
    }

    public function getUserProductRating(int $usuarioId, int $productoId): ?float
    {
        $this->ensureTablesExist();

        $sql = 'SELECT rating FROM ratings_productos WHERE usuario_id = ? AND producto_id = ?';
        $value = $this->fetchColumn($sql, [$usuarioId, $productoId]);
        return $value !== null ? (float) $value : null;
    }

    private function validateRating(float $rating): void
    {
        if ($rating < 1 || $rating > 10) {
            throw new ValidationException(['rating' => 'La valoración debe estar entre 1 y 10']);
        }
    }
}
