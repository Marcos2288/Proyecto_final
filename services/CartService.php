<?php

class CartService extends Service
{
    public function getCart(): array
    {
        return $_SESSION['carrito'] ?? [];
    }

    public function getItemQuantities(): array
    {
        return array_count_values(array_map('intval', $this->getCart()));
    }

    public function addItem(int $productoId, int $cantidad = 1): void
    {
        if ($cantidad < 1) {
            throw new ValidationException(['cantidad' => 'La cantidad debe ser al menos 1']);
        }

        $_SESSION['carrito'] = array_merge($this->getCart(), array_fill(0, $cantidad, $productoId));
    }

    public function updateQuantity(int $productoId, int $cantidad): void
    {
        if ($cantidad < 0) {
            throw new ValidationException(['cantidad' => 'Cantidad inválida']);
        }

        $cart = $this->getCart();
        $cart = array_values(array_filter($cart, fn($id) => (int) $id !== $productoId));
        $_SESSION['carrito'] = array_merge($cart, array_fill(0, $cantidad, $productoId));
    }

    public function removeItem(int $productoId): void
    {
        $_SESSION['carrito'] = array_values(array_filter($this->getCart(), fn($id) => (int) $id !== $productoId));
    }

    public function clear(): void
    {
        $_SESSION['carrito'] = [];
    }

    public function checkout(int $usuarioId, float $total): void
    {
        $this->validateCheckout();

        $quantities = $this->getItemQuantities();
        if (empty($quantities)) {
            throw new ValidationException(['carrito' => 'El carrito está vacío']);
        }

        $ids = array_keys($quantities);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $this->beginTransaction();
        try {
            $stmt = $this->connection->prepare("SELECT id, precio, stock FROM productos WHERE id IN ($placeholders) FOR UPDATE");
            $stmt->execute($ids);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$productos) {
                throw new Exception('No se encontraron productos validos en el carrito.');
            }

            $this->execute("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)", [$usuarioId, $total]);
            $pedidoId = (int) $this->lastInsertId();

            $stockUpdate = $this->connection->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $insertDetalle = $this->connection->prepare("INSERT INTO pedido_detalle (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
            $comprasInsert = $this->connection->prepare("INSERT INTO compras (usuario_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");

            foreach ($productos as $producto) {
                $productoId = (int) $producto['id'];
                $cantidad = $quantities[$productoId] ?? 0;
                if ($cantidad <= 0) {
                    continue;
                }

                if ((int) $producto['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para el producto ID $productoId.");
                }

                $stockUpdate->execute([$cantidad, $productoId, $cantidad]);
                if ($stockUpdate->rowCount() === 0) {
                    throw new Exception("No se pudo actualizar el stock del producto ID $productoId.");
                }

                $insertDetalle->execute([$pedidoId, $productoId, $cantidad]);
                $comprasInsert->execute([$usuarioId, $productoId, $cantidad, (float) $producto['precio']]);
            }

            $this->commit();
            $this->clear();
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->rollback();
            }
            throw $e;
        }
    }

    public function getCartWithDetails(array|null $user = null): array
    {
        $quantities = $this->getItemQuantities();
        if (!$quantities) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($quantities), '?'));
        $stmt = $this->connection->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($quantities));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($product) use ($quantities, $user) {
            $id = (int) $product['id'];
            $cantidad = $quantities[$id] ?? 0;
            $price = PricingCalculator::calculatePrice((float) $product['precio'], $user);
            return array_merge($product, ['cantidad' => $cantidad, 'precio_final' => $price]);
        }, $products);
    }

    public function getTotal(array|null $user = null): float
    {
        $items = $this->getCartWithDetails($user);
        return PricingCalculator::calculateCartTotal($items, $user);
    }

    public function getDiscount(array|null $user = null): float
    {
        $subtotal = $this->getTotal($user);
        return PricingCalculator::calculateCartDiscount($subtotal, $user);
    }

    public function validateCheckout(): void
    {
        $cart = $this->getItemQuantities();
        if (empty($cart)) {
            throw new ValidationException(['carrito' => 'El carrito está vacío']);
        }

        foreach ($this->getCartWithDetails() as $item) {
            if ((int) $item['stock'] < (int) $item['cantidad']) {
                throw new InsufficientStockException((int) $item['stock'], (int) $item['cantidad']);
            }
        }
    }
}
