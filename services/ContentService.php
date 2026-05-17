<?php

class ContentService extends Service
{
    public function getAllContent(int $usuarioId = null): array
    {
        if ($usuarioId) {
            $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                    FROM contenidos c
                    LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                    WHERE c.id NOT IN (SELECT contenido_id FROM historial WHERE usuario_id = ?)
                    GROUP BY c.id
                    ORDER BY c.id DESC";
            return $this->fetchAll($sql, [$usuarioId]);
        }

        $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                FROM contenidos c
                LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                GROUP BY c.id
                ORDER BY c.id DESC";
        return $this->fetchAll($sql);
    }

    public function getContentById(int $id): ?array
    {
        $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                FROM contenidos c
                LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                WHERE c.id = ?
                GROUP BY c.id";
        return $this->fetch($sql, [$id]);
    }

    public function getContentByType(string $type): array
    {
        $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                FROM contenidos c
                LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                WHERE LOWER(c.tipo) = ?
                GROUP BY c.id
                ORDER BY c.titulo ASC";
        return $this->fetchAll($sql, [strtolower($type)]);
    }

    public function getAllContentsOrderedByTitle(): array
    {
        $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                FROM contenidos c
                LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                GROUP BY c.id
                ORDER BY c.titulo ASC";
        return $this->fetchAll($sql);
    }

    public function getAllContents(): array
    {
        return $this->getAllContent();
    }

    public function getRandomContents(int $limit = 6): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT c.*, COALESCE(AVG(rc.rating), 0) as avg_rating, COUNT(rc.id) as total_ratings
                FROM contenidos c
                LEFT JOIN ratings_contenidos rc ON c.id = rc.contenido_id
                GROUP BY c.id
                ORDER BY RAND()
                LIMIT " . $limit;
        return $this->fetchAll($sql);
    }

    public function searchContentByTitle(string $query, int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT id, titulo
                FROM contenidos
                WHERE titulo LIKE ?
                ORDER BY titulo ASC
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$query]);
    }

    public function searchContentsByTitle(string $query, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT * FROM contenidos
                WHERE titulo LIKE ?
                ORDER BY id DESC
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$query]);
    }

    public function searchEpisodes(string $query, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT e.*, c.titulo AS serie, c.imagen
                FROM episodios e
                JOIN contenidos c ON c.id = e.serie_id
                WHERE e.titulo LIKE ?
                ORDER BY c.titulo, e.temporada, e.numero
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$query]);
    }

    public function getEpisodes(int $serieId, int $temporada = null): array
    {
        if ($temporada !== null) {
            $sql = 'SELECT * FROM episodios WHERE serie_id = ? AND temporada = ? ORDER BY numero ASC';
            return $this->fetchAll($sql, [$serieId, $temporada]);
        }

        $sql = 'SELECT * FROM episodios WHERE serie_id = ? ORDER BY temporada ASC, numero ASC';
        return $this->fetchAll($sql, [$serieId]);
    }

    public function getSeasons(int $serieId): array
    {
        $sql = 'SELECT DISTINCT temporada FROM episodios WHERE serie_id = ? ORDER BY temporada ASC';
        $rows = $this->fetchAll($sql, [$serieId]);
        return array_map(fn($row) => (int) $row['temporada'], $rows);
    }

    public function createContent(string $titulo, string $tipo, string $imagen, string $descripcion = ''): int
    {
        $this->validateContent($titulo, $tipo, $imagen);

        $sql = 'INSERT INTO contenidos (titulo, tipo, imagen, descripcion) VALUES (?, ?, ?, ?)';
        $this->execute($sql, [$titulo, strtolower($tipo), $imagen, $descripcion]);

        return (int) $this->lastInsertId();
    }

    public function createEpisode(int $serieId, int $temporada, int $numero, string $titulo, string $imagen, string $descripcion = ''): int
    {
        $this->validateEpisode($serieId, $temporada, $numero, $titulo, $imagen);

        $sql = 'INSERT INTO episodios (serie_id, temporada, numero, titulo, imagen, descripcion) VALUES (?, ?, ?, ?, ?, ?)';
        $this->execute($sql, [$serieId, $temporada, $numero, $titulo, $imagen, $descripcion]);

        return (int) $this->lastInsertId();
    }

    public function markEpisodeAsWatched(int $usuarioId, int $episodioId): void
    {
        $this->ensureEpisodeHistoryTable();

        $sql = 'INSERT INTO episodios_vistos (usuario_id, episodio_id, visto)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE visto = 1, fecha_visto = CURRENT_TIMESTAMP';
        $this->execute($sql, [$usuarioId, $episodioId]);
    }

    public function markContentAsWatched(int $usuarioId, int $contenidoId): void
    {
        $sql = 'INSERT INTO historial (usuario_id, contenido_id, visto)
                SELECT ?, ?, 1
                WHERE NOT EXISTS (
                    SELECT 1 FROM historial WHERE usuario_id = ? AND contenido_id = ? AND visto = 1
                )';
        $this->execute($sql, [$usuarioId, $contenidoId, $usuarioId, $contenidoId]);

        $episodes = $this->fetchAll('SELECT id FROM episodios WHERE serie_id = ?', [$contenidoId]);
        if ($episodes) {
            $this->ensureEpisodeHistoryTable();
            $sql = 'INSERT INTO episodios_vistos (usuario_id, episodio_id, visto)
                    VALUES (?, ?, 1)
                    ON DUPLICATE KEY UPDATE visto = 1, fecha_visto = CURRENT_TIMESTAMP';
            foreach ($episodes as $episode) {
                $this->execute($sql, [$usuarioId, (int) $episode['id']]);
            }
        }
    }

    public function getSeriesProgress(int $usuarioId, int $serieId): array
    {
        $this->ensureEpisodeHistoryTable();

        $total = (int) $this->fetchColumn('SELECT COUNT(*) FROM episodios WHERE serie_id = ?', [$serieId]);
        $watched = (int) $this->fetchColumn(
            'SELECT COUNT(DISTINCT ev.episodio_id)
             FROM episodios_vistos ev
             JOIN episodios e ON e.id = ev.episodio_id
             WHERE ev.usuario_id = ? AND e.serie_id = ? AND ev.visto = 1',
            [$usuarioId, $serieId]
        );

        $percentage = $total > 0 ? (int) round(($watched / $total) * 100) : 0;
        return ['total' => $total, 'watched' => $watched, 'percentage' => $percentage];
    }

    public function getViewedContentIds(int $usuarioId): array
    {
        if (!$usuarioId) {
            return [];
        }

        $rows = $this->fetchAll('SELECT contenido_id FROM historial WHERE usuario_id = ? AND visto = 1', [$usuarioId]);
        return array_fill_keys(array_map('intval', array_column($rows, 'contenido_id')), true);
    }

    public function getWatchedEpisodeIds(int $usuarioId, int $serieId): array
    {
        if (!$usuarioId || !$serieId) {
            return [];
        }

        $this->ensureEpisodeHistoryTable();
        $rows = $this->fetchAll(
            'SELECT ev.episodio_id
             FROM episodios_vistos ev
             JOIN episodios e ON e.id = ev.episodio_id
             WHERE ev.usuario_id = ? AND ev.visto = 1 AND e.serie_id = ?'
            , [$usuarioId, $serieId]
        );

        return array_map(fn($row) => (int) $row['episodio_id'], $rows);
    }

    public function getMaxSeasonViewedPerContent(int $usuarioId): array
    {
        if (!$usuarioId) {
            return [];
        }

        $this->ensureEpisodeHistoryTable();
        $rows = $this->fetchAll(
            'SELECT e.serie_id, MAX(e.temporada) as max_temporada
             FROM episodios_vistos ev
             JOIN episodios e ON e.id = ev.episodio_id
             WHERE ev.usuario_id = ? AND ev.visto = 1
             GROUP BY e.serie_id',
            [$usuarioId]
        );

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['serie_id']] = (int) $row['max_temporada'];
        }
        return $result;
    }

    private function validateContent(string $titulo, string $tipo, string $imagen): void
    {
        $errors = [];

        if (trim($titulo) === '' || strlen($titulo) < 3) {
            $errors['titulo'] = 'El título debe tener al menos 3 caracteres';
        }

        if (!in_array(strtolower($tipo), ['pelicula', 'serie'], true)) {
            $errors['tipo'] = 'El tipo debe ser película o serie';
        }

        if (!filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errors['imagen'] = 'La URL de la imagen es inválida';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    private function validateEpisode(int $serieId, int $temporada, int $numero, string $titulo, string $imagen): void
    {
        $errors = [];

        $serie = $this->fetch('SELECT id FROM contenidos WHERE id = ? AND LOWER(tipo) = ?', [$serieId, 'serie']);
        if ($serie === null) {
            $errors['serie'] = 'La serie no existe';
        }

        if ($temporada < 1) {
            $errors['temporada'] = 'La temporada debe ser mayor o igual a 1';
        }

        if ($numero < 1) {
            $errors['numero'] = 'El número de episodio debe ser mayor o igual a 1';
        }

        if (trim($titulo) === '' || strlen($titulo) < 3) {
            $errors['titulo'] = 'El título debe tener al menos 3 caracteres';
        }

        if (!filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errors['imagen'] = 'La URL de la imagen es inválida';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    private function ensureEpisodeHistoryTable(): void
    {
        $this->connection->exec(
            "CREATE TABLE IF NOT EXISTS episodios_vistos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                episodio_id INT NOT NULL,
                visto TINYINT(1) DEFAULT 1,
                fecha_visto DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY usuario_episodio (usuario_id, episodio_id),
                KEY episodio_id (episodio_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }
}
