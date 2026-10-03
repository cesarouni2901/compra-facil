<?php
/** Controlador para publicar productos y aceptar varias fotografías seguras. */
class ProductController
{
    public function __construct(private ProductCatalog $catalog) {}

    /** Solo un vendedor autenticado puede crear un producto. */
    public function create(): string
    {
        $user = authenticatedUser();
        if (!$user || $user['role'] !== 'vendedor' || empty($user['is_adult'])) {
            http_response_code(403);
            return 'Debes iniciar sesión con una cuenta de vendedor adulta para publicar productos.';
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return '';
        if (!validateCsrfToken((string) ($_POST['csrf_token'] ?? ''))) return 'La sesión del formulario venció. Recarga la página.';

        $name = trim((string) ($_POST['name'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $category = trim((string) ($_POST['category'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $basePrice = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($name === '' || $brand === '' || $category === '' || $description === '' || $basePrice === false || $basePrice <= 0) {
            return 'Completa el nombre, marca, categoría, descripción y un precio mayor a cero.';
        }

        $variants = [];
        $colors = $_POST['colors'] ?? [];
        $storages = $_POST['storages'] ?? [];
        $prices = $_POST['variant_prices'] ?? [];
        for ($index = 0; $index < 6; $index++) {
            $color = trim((string) ($colors[$index] ?? ''));
            $storage = trim((string) ($storages[$index] ?? ''));
            if ($color === '' && $storage === '') continue;
            $variantPrice = filter_var($prices[$index] ?? null, FILTER_VALIDATE_FLOAT);
            $variants[] = ['color' => $color, 'storage' => $storage, 'price' => $variantPrice !== false && $variantPrice > 0 ? $variantPrice : (float) $basePrice];
        }
        if (!$variants) $variants[] = ['color' => '', 'storage' => '', 'price' => (float) $basePrice];

        try {
            $images = $this->saveUploadedImages($_FILES['images'] ?? [], $_POST['image_views'] ?? []);
            if (!$images) return 'Sube al menos una imagen del producto.';
            $id = $this->catalog->add([
                'name' => $name,
                'brand' => $brand,
                'category' => $category,
                'description' => $description,
                'price' => (float) $basePrice,
                'features' => ['Marca' => $brand],
                'variants' => $variants,
                'images' => $images,
            ]);
            header('Location: producto.php?id=' . rawurlencode($id));
            exit;
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }
    }

    /** Comprueba MIME real, tamaño y extensión antes de mover cada imagen. */
    private function saveUploadedImages(array $upload, array $viewLabels): array
    {
        if (empty($upload['name']) || !is_array($upload['name'])) return [];
        if (count($upload['name']) > 6) throw new RuntimeException('Puedes subir hasta 6 fotos por producto.');
        $targetDirectory = dirname(__DIR__, 2) . '/uploads/products';
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true)) throw new RuntimeException('No se pudo preparar la carpeta de imágenes.');
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $allowedViews = ['Frontal', 'Lateral', 'Posterior', 'Abierta', 'Detalle', 'Otra vista'];
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $images = [];
        foreach ($upload['name'] as $index => $originalName) {
            $error = $upload['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) continue;
            if ($error !== UPLOAD_ERR_OK) throw new RuntimeException('Una de las imágenes no se pudo cargar.');
            $temporaryPath = $upload['tmp_name'][$index] ?? '';
            $size = (int) ($upload['size'][$index] ?? 0);
            $mimeType = is_uploaded_file($temporaryPath) ? $fileInfo->file($temporaryPath) : false;
            if ($size < 1 || $size > 1024 * 1024 || !isset($allowedTypes[$mimeType])) {
                throw new RuntimeException('Cada imagen debe ser JPG, PNG o WebP y pesar máximo 1 MB.');
            }
            $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
            if (!move_uploaded_file($temporaryPath, $targetDirectory . '/' . $fileName)) throw new RuntimeException('No se pudo guardar una imagen.');
            $view = (string) ($viewLabels[$index] ?? 'Otra vista');
            $images[] = ['path' => 'uploads/products/' . $fileName, 'view_label' => in_array($view, $allowedViews, true) ? $view : 'Otra vista'];
        }
        return $images;
    }
}
