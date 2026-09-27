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
            if ($this->catalog->findById($id)) {
                if ($action === 'add') $this->cart->add($id);
                if ($action === 'increase') $this->cart->change($id, 1);
                if ($action === 'decrease') $this->cart->change($id, -1);
                if ($action === 'remove') $this->cart->remove($id);
            }
            if ($action === 'checkout' && $this->cart->getCount() > 0) {
                $payment = (string) ($_POST['payment'] ?? '');
                $_SESSION['checkout_message'] = in_array($payment, ['pago-movil', 'tarjeta', 'paypal'], true)
                    ? 'Pedido de demostración preparado. El pago se habilitará al conectar la pasarela correspondiente.'
                    : 'Selecciona un método de pago para continuar.';
            }
            header('Location: carrito.php');
            exit;
        }

        $items = [];
        $total = 0.0;
        foreach ($this->cart->getItems() as $id => $quantity) {
            $product = $this->catalog->findById((string) $id);
            if (!$product) continue;
            $items[] = ['product' => $product, 'quantity' => (int) $quantity];
            $total += $product->price * (int) $quantity;
        }
        $message = (string) ($_SESSION['checkout_message'] ?? '');
        unset($_SESSION['checkout_message']);
        return ['items' => $items, 'total' => $total, 'message' => $message];
    }
}
