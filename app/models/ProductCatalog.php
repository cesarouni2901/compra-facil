<?php
/** Modelo con los productos y las reglas de búsqueda y filtrado. */
class ProductCatalog
{
    private array $products;

    public function __construct()
    {
        $records = [
            ['id' => 'audifonos', 'name' => 'Audífonos inalámbricos', 'category' => 'Audio', 'price' => 39.99, 'description' => 'Sonido claro para escuchar música todos los días.', 'features' => ['Tipo' => 'Inalámbricos', 'Batería' => 'Hasta 20 horas', 'Garantía' => '1 año'], 'rating' => 4.8, 'reviews' => 24],
            ['id' => 'laptop', 'name' => 'Laptop básica 14 pulgadas', 'category' => 'Computación', 'price' => 429.00, 'description' => 'Una opción sencilla para estudiar y trabajar.', 'features' => ['Pantalla' => '14 pulgadas', 'Memoria' => '8 GB', 'Almacenamiento' => '256 GB SSD'], 'rating' => 4.5, 'reviews' => 18],
            ['id' => 'telefono', 'name' => 'Teléfono inteligente', 'category' => 'Telefonía', 'price' => 219.50, 'description' => 'Pantalla amplia y espacio para tus aplicaciones.', 'features' => ['Pantalla' => '6.5 pulgadas', 'Memoria' => '6 GB', 'Almacenamiento' => '128 GB'], 'rating' => 4.6, 'reviews' => 31],
        ];
        $this->products = array_map(fn (array $record): Product => new Product($record), $records);
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
            $text = mb_strtolower($product->name . ' ' . $product->category . ' ' . $product->description, 'UTF-8');
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
}
