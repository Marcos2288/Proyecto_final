<?php

class ProductService extends Service
{
    public function getAllProducts(): array
    {
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                GROUP BY p.id
                ORDER BY p.id DESC";
        return $this->fetchAll($sql);
    }

    public function getProductById(int $id): ?array
    {
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                WHERE p.id = ?
                GROUP BY p.id";
        return $this->fetch($sql, [$id]);
    }

    public function getProductsByCategory(string $categoria, int $excludeId = null): array
    {
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                WHERE p.categoria = ?";

        $params = [$categoria];
        if ($excludeId) {
            $sql .= ' AND p.id != ?';
            $params[] = $excludeId;
        }

        $sql .= ' GROUP BY p.id ORDER BY p.id DESC';
        return $this->fetchAll($sql, $params);
    }

    public function getProductsByContent(int $contenidoId): array
    {
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                WHERE p.contenido_id = ?
                GROUP BY p.id
                ORDER BY p.id DESC";
        return $this->fetchAll($sql, [$contenidoId]);
    }

    public function getProductsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                WHERE p.id IN ($placeholders)
                GROUP BY p.id";
        return $this->fetchAll($sql, $ids);
    }

    public function getRecommendationsByCategory(string $categoria, int $excludeId = null, int $limit = 12): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT p.*, COALESCE(AVG(rp.rating), 0) as avg_rating, COUNT(rp.id) as total_ratings
                FROM productos p
                LEFT JOIN ratings_productos rp ON p.id = rp.producto_id
                WHERE p.categoria = ?";
        $params = [$categoria];

        if ($excludeId) {
            $sql .= ' AND p.id != ?';
            $params[] = $excludeId;
        }

        $sql .= ' GROUP BY p.id ORDER BY p.id DESC LIMIT ' . $limit;

        return $this->fetchAll($sql, $params);
    }

    public function createProduct(string $nombre, float $precio, string $imagen, string $categoria, string $descripcion = '', string $detalles = '', int $stock = 0, int $contenidoId = null, int $temporada = null, float $rating = 0.0): int
    {
        $this->validateProduct($nombre, $precio, $imagen, $categoria);
        $this->validateRating($rating);

        $sql = "INSERT INTO productos (nombre, precio, imagen, categoria, descripcion, detalles, stock, rating, contenido_id, temporada)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [$nombre, $precio, $imagen, $categoria, $descripcion, $detalles, $stock, $rating, $contenidoId ?: null, $temporada ?: null]);

        return (int) $this->lastInsertId();
    }

    public function searchProductsByName(string $query, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT * FROM productos
                WHERE nombre LIKE ?
                ORDER BY id DESC
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$query]);
    }

    public function updateStock(int $productoId, int $newStock): void
    {
        if ($newStock < 0) {
            throw new ValidationException(['stock' => 'El stock no puede ser negativo']);
        }
        $this->execute('UPDATE productos SET stock = ? WHERE id = ?', [$newStock, $productoId]);
    }

    public function hasUserPurchasedProduct(int $usuarioId, int $productoId): bool
    {
        $sql = 'SELECT COUNT(*) FROM compras WHERE usuario_id = ? AND producto_id = ?';
        return (int) $this->fetchColumn($sql, [$usuarioId, $productoId]) > 0;
    }

    private function validateProduct(string $nombre, float $precio, string $imagen, string $categoria): void
    {
        $errors = [];

        if (trim($nombre) === '' || strlen($nombre) < 3) {
            $errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if ($precio <= 0) {
            $errors['precio'] = 'El precio debe ser mayor a 0';
        }

        if (!filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errors['imagen'] = 'La URL de la imagen es inválida';
        }

        if (trim($categoria) === '') {
            $errors['categoria'] = 'La categoría es requerida';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    private function validateRating(float $rating): void
    {
        if ($rating < 0 || $rating > 10) {
            throw new ValidationException(['rating' => 'La valoración debe estar entre 0 y 10']);
        }
    }
}
