<?php
/** Controlador de acciones y página del carrito. */
class CartController
{
    public function __construct(private ShoppingCart $cart, private ProductCatalog $catalog) {}

    /** Ejecuta operaciones del formulario y devuelve los datos para la vista. */
    public function index(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');
            $id = (string) ($_POST['product_id'] ?? '');
            $variantId = (string) ($_POST['variant_id'] ?? 'default');
            if (!validateCsrfToken((string) ($_POST['csrf_token'] ?? ''))) {
                $_SESSION['checkout_message'] = 'El formulario venció. Recarga la página e inténtalo otra vez.';
                header('Location: carrito.php');
                exit;
            }
            $itemKey = ShoppingCart::itemKey($id, $variantId);
            $product = $this->catalog->findById($id);
            $variant = $product ? $this->catalog->findVariant($product, $variantId) : null;
            if ($product && ($variant || $variantId === 'default')) {
                if ($action === 'add') $this->cart->add($id, $variantId);
                if ($action === 'increase') $this->cart->change($itemKey, 1);
                if ($action === 'decrease') $this->cart->change($itemKey, -1);
                if ($action === 'remove') $this->cart->remove($itemKey);
            }
            if ($action === 'checkout' && $this->cart->getCount() > 0) {
                $buyer = authenticatedUser();
                $payment = (string) ($_POST['payment'] ?? '');
                $_SESSION['checkout_message'] = !$buyer || empty($buyer['is_adult'])
                    ? 'Inicia sesión con una cuenta de persona adulta para comprar.'
                    : (in_array($payment, ['pago-movil', 'tarjeta', 'paypal'], true)
                    ? 'Pedido de demostración preparado. El pago se habilitará al conectar la pasarela correspondiente.'
                    : 'Selecciona un método de pago para continuar.');
            }
            header('Location: carrito.php');
            exit;
        }

        $items = [];
        $total = 0.0;
        foreach ($this->cart->getItems() as $key => $quantity) {
            [$id, $variantId] = array_pad(explode('::', (string) $key, 2), 2, 'default');
            $product = $this->catalog->findById($id);
            if (!$product) continue;
            $variant = $this->catalog->findVariant($product, $variantId);
            $price = (float) ($variant['price'] ?? $product->price);
            $items[] = ['product' => $product, 'variant' => $variant, 'variant_id' => $variantId, 'quantity' => (int) $quantity, 'price' => $price, 'item_key' => $key];
            $total += $price * (int) $quantity;
        }
        $message = (string) ($_SESSION['checkout_message'] ?? '');
        unset($_SESSION['checkout_message']);
        return ['items' => $items, 'total' => $total, 'message' => $message];
    }
}
