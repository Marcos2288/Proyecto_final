<?php

class AuthService extends Service
{
    public function register(string $nombre, string $email, string $password, string $tipoPlan = 'basico'): int
    {
        $tipoPlan = in_array(strtolower($tipoPlan), ['basico', 'premium'], true) ? strtolower($tipoPlan) : 'basico';
        $this->validateRegistration($nombre, $email, $password);

        if ($this->userExists($email)) {
            throw new ValidationException(['email' => 'El correo ya está en uso']);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO usuarios (nombre, email, password, tipo_plan) VALUES (?, ?, ?, ?)';
        $this->execute($sql, [$nombre, $email, $hash, $tipoPlan]);

        return (int) $this->lastInsertId();
    }

    public function login(string $email, string $password): array
    {
        $user = $this->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'] ?? '')) {
            throw new AuthenticationException('Credenciales inválidas');
        }

        $this->setSession($user);
        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function isAuthenticated(): bool
    {
        return !empty($_SESSION['usuario']);
    }

    public function getCurrentUser(): ?array
    {
        if (empty($_SESSION['usuario'])) {
            return null;
        }

        return $this->findById((int) $_SESSION['usuario']);
    }

    public function getPurchaseHistory(int $userId, int $limit = 8): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT p.fecha, p.total AS pedido_total, pd.cantidad, pr.nombre, pr.precio
                FROM pedidos p
                JOIN pedido_detalle pd ON pd.pedido_id = p.id
                JOIN productos pr ON pr.id = pd.producto_id
                WHERE p.usuario_id = ?
                ORDER BY p.fecha DESC, pd.id DESC
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$userId]);
    }

    public function getViewedHistory(int $userId, int $limit = 5): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT c.titulo
                FROM historial h
                JOIN contenidos c ON c.id = h.contenido_id
                WHERE h.usuario_id = ?
                ORDER BY h.id DESC
                LIMIT " . $limit;
        return $this->fetchAll($sql, [$userId]);
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM usuarios WHERE id = ?';
        return $this->fetch($sql, [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM usuarios WHERE email = ?';
        return $this->fetch($sql, [$email]);
    }

    public function isDeveloper(): bool
    {
        if (isset($_SESSION['desarrollador'])) {
            return $_SESSION['desarrollador'] === 1;
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }

        $developer = (int) ($user['desarrollador'] ?? 0);
        $_SESSION['desarrollador'] = $developer;
        return $developer === 1;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $fields = [];
        $params = [];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = ?';
            $params[] = trim($data['nombre']);
        }

        if (isset($data['tipo_plan'])) {
            $plan = in_array(strtolower($data['tipo_plan']), ['basico', 'premium'], true) ? strtolower($data['tipo_plan']) : 'basico';
            $fields[] = 'tipo_plan = ?';
            $params[] = $plan;
        }

        if (isset($data['modo_anti_spoiler'])) {
            $fields[] = 'spoiler_mode = ?';
            $params[] = (int) $data['modo_anti_spoiler'];
        }

        if (!$fields) {
            return;
        }

        $params[] = $userId;
        $sql = 'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $this->execute($sql, $params);

        $user = $this->findById($userId);
        $this->setSession($user ?: []);
    }

    private function setSession(array $user): void
    {
        $_SESSION['usuario'] = (int) ($user['id'] ?? 0);
        $_SESSION['tipo_plan'] = strtolower((string) ($user['tipo_plan'] ?? 'basico'));
        $_SESSION['modo_anti_spoiler'] = (int) ($user['spoiler_mode'] ?? $user['modo_anti_spoiler'] ?? 0);
        $_SESSION['desarrollador'] = (int) ($user['desarrollador'] ?? 0);
    }

    private function validateRegistration(string $nombre, string $email, string $password): void
    {
        $errors = [];

        if (trim($nombre) === '' || strlen($nombre) < 3) {
            $errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email es inválido';
        }

        if (strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    private function userExists(string $email): bool
    {
        return (bool) $this->fetchColumn('SELECT COUNT(*) FROM usuarios WHERE email = ?', [$email]);
    }
}
