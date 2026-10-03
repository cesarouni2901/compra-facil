<?php
/** Modelo con los productos y las reglas de búsqueda y filtrado. */
class ProductCatalog
{
    private array $products;

    public function __construct()
    {
        $records = [
            ['id' => 'audifonos', 'name' => 'Audífonos inalámbricos', 'brand' => 'Sonix', 'category' => 'Audio', 'price' => 39.99, 'description' => 'Sonido claro para escuchar música todos los días.', 'features' => ['Tipo' => 'Inalámbricos', 'Batería' => 'Hasta 20 horas', 'Garantía' => '1 año'], 'rating' => 4.8, 'reviews' => 24, 'variants' => [['id' => 'audifonos-base', 'color' => 'Negro', 'storage' => '', 'price' => 39.99]], 'images' => []],
            ['id' => 'laptop', 'name' => 'Laptop básica 14 pulgadas', 'brand' => 'Nova', 'category' => 'Computación', 'price' => 429.00, 'description' => 'Una opción sencilla para estudiar y trabajar.', 'features' => ['Pantalla' => '14 pulgadas', 'Memoria' => '8 GB', 'Almacenamiento' => '256 GB SSD'], 'rating' => 4.5, 'reviews' => 18, 'variants' => [['id' => 'laptop-base', 'color' => 'Gris', 'storage' => '256 GB SSD', 'price' => 429.00]], 'images' => []],
            ['id' => 'telefono', 'name' => 'Teléfono inteligente', 'brand' => 'Movilux', 'category' => 'Telefonía', 'price' => 219.50, 'description' => 'Pantalla amplia y espacio para tus aplicaciones.', 'features' => ['Pantalla' => '6.5 pulgadas', 'Memoria' => '6 GB', 'Almacenamiento' => '128 GB'], 'rating' => 4.6, 'reviews' => 31, 'variants' => [['id' => 'telefono-negro-128', 'color' => 'Negro', 'storage' => '128 GB', 'price' => 219.50], ['id' => 'telefono-azul-256', 'color' => 'Azul', 'storage' => '256 GB', 'price' => 259.50], ['id' => 'telefono-verde-128', 'color' => 'Verde', 'storage' => '128 GB', 'price' => 219.50]], 'images' => []],
        ];
        $storedRecords = $this->readStoredProducts();
        $this->products = array_map(fn (array $record): Product => new Product($record), [...$records, ...$storedRecords]);
    }

    /** Devuelve las categorías disponibles para el selector del catálogo. */
    public function getCategories(): array
    {
        $categories = array_map(fn (Product $product): string => $product->category, $this->products);
        $categories = array_unique($categories);
        sort($categories, SORT_NATURAL | SORT_FLAG_CASE);
        return $categories;
    }

    /** Aplica búsqueda, categoría, precio máximo y orden elegido por el usuario. */
    public function filter(string $term, string $category, ?float $maxPrice, string $sort): array
    {
        $query = mb_strtolower(trim($term), 'UTF-8');
        $results = array_filter($this->products, function (Product $product) use ($query, $category, $maxPrice): bool {
            $text = mb_strtolower($product->name . ' ' . $product->brand . ' ' . $product->category . ' ' . $product->description, 'UTF-8');
            return ($query === '' || mb_strpos($text, $query, 0, 'UTF-8') !== false)
                && ($category === '' || $product->category === $category)
                && ($maxPrice === null || $product->price <= $maxPrice);
        });

        // Ordena los resultados filtrados por precio o valoración cuando se solicita.
        usort($results, function (Product $first, Product $second) use ($sort): int {
            if ($sort === 'price-asc') return $first->price <=> $second->price;
            if ($sort === 'price-desc') return $second->price <=> $first->price;
            if ($sort === 'rating') return $second->rating <=> $first->rating;
            return 0;
        });
        return array_values($results);
    }

    /** Busca un producto por su identificador para mostrarlo en el carrito. */
    public function findById(string $id): ?Product
    {
        foreach ($this->products as $product) {
            if ($product->id === $id) return $product;
        }
        return null;
    }

    /** Busca una variante perteneciente al producto solicitado. */
    public function findVariant(Product $product, string $variantId): ?array
    {
        foreach ($product->variants as $variant) {
            if ((string) ($variant['id'] ?? '') === $variantId) return $variant;
        }
        return null;
    }

    /** Guarda producto, variantes y metadatos de fotos en el archivo protegido. */
    public function add(array $data): string
    {
        ensurePrivateStorage();
        $path = dirname(__DIR__) . '/private/products.json';
        $lock = fopen(dirname($path) . '/.products.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) throw new RuntimeException('No se pudo bloquear el archivo de productos.');
        try {
            $records = is_file($path) ? json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR) : [];
            $id = 'seller-' . bin2hex(random_bytes(8));
            $data['id'] = $id;
            $data['seller_id'] = authenticatedUser()['id'];
            $data['rating'] = 0;
            $data['reviews'] = 0;
            foreach ($data['variants'] as $index => &$variant) $variant['id'] = $id . '-variant-' . ($index + 1);
            unset($variant);
            $records[] = $data;
            $temporaryPath = $path . '.tmp';
            $json = json_encode($records, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            if (file_put_contents($temporaryPath, $json, LOCK_EX) === false || !rename($temporaryPath, $path)) {
                throw new RuntimeException('No se pudo guardar el producto en el archivo local.');
            }
            return $id;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /** Lee productos creados por vendedores; el archivo nunca se sirve al navegador. */
    private function readStoredProducts(): array
    {
        $path = dirname(__DIR__) . '/private/products.json';
        if (!is_file($path)) return [];
        $records = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        return is_array($records) ? $records : [];
    }
}
