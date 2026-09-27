<?php
/** Controlador de la página de catálogo. */
class CatalogController
{
    public function __construct(private ProductCatalog $catalog) {}

    /** Lee filtros HTTP GET y prepara los datos que necesita la vista. */
    public function index(): array
    {
        $term = trim((string) ($_GET['q'] ?? ''));
        $category = trim((string) ($_GET['category'] ?? ''));
        $rawPrice = trim((string) ($_GET['max_price'] ?? ''));
        $maxPrice = $rawPrice !== '' && is_numeric($rawPrice) && (float) $rawPrice >= 0 ? (float) $rawPrice : null;
        $sort = (string) ($_GET['sort'] ?? 'featured');
        if (!in_array($sort, ['featured', 'price-asc', 'price-desc', 'rating'], true)) $sort = 'featured';

        return [
            'products' => $this->catalog->filter($term, $category, $maxPrice, $sort),
            'categories' => $this->catalog->getCategories(),
            'filters' => ['q' => $term, 'category' => $category, 'max_price' => $rawPrice, 'sort' => $sort],
        ];
    }
}
