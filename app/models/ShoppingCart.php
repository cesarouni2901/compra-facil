<?php
/** Modelo del carrito; persiste cantidades en la sesión PHP del visitante. */
class ShoppingCart
{
    private array $items;

    public function __construct()
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];
        $this->items = $_SESSION['cart'];
    }

    /** Agrega una unidad del producto a la sesión. */
    public function add(string $id, string $variantId = 'default'): void
    {
        $key = $id . '::' . $variantId;
        $this->items[$key] = ($this->items[$key] ?? 0) + 1;
        $this->save();
    }

    /** Cambia la cantidad y elimina el producto si llega a cero. */
    public function change(string $id, int $amount): void
    {
        if (!isset($this->items[$id])) return;
        $this->items[$id] += $amount;
        if ($this->items[$id] <= 0) unset($this->items[$id]);
        $this->save();
    }

    /** Elimina todas las unidades de un producto. */
    public function remove(string $id): void
    {
        unset($this->items[$id]);
        $this->save();
    }

    /** Devuelve las cantidades indexadas por identificador de producto. */
    public function getItems(): array { return $this->items; }

    /** Forma una clave estable para el producto y la variante que eligió el cliente. */
    public static function itemKey(string $id, string $variantId = 'default'): string { return $id . '::' . $variantId; }

    /** Cuenta las unidades para el indicador del encabezado. */
    public function getCount(): int { return array_sum($this->items); }

    /** Guarda el estado actual en la sesión. */
    private function save(): void { $_SESSION['cart'] = $this->items; }
}
