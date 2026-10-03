<?php
/** Modelo de datos de un producto del catálogo. */
class Product
{
    public string $id;
    public string $name;
    public string $category;
    public string $brand;
    public float $price;
    public string $description;
    public array $features;
    public float $rating;
    public int $reviews;
    public string $image;
    public array $images;
    public array $variants;
    public ?int $sellerId;

    public function __construct(array $data)
    {
        $this->id = (string) $data['id'];
        $this->name = (string) $data['name'];
        $this->category = (string) $data['category'];
        $this->brand = (string) ($data['brand'] ?? '');
        $this->price = (float) $data['price'];
        $this->description = (string) $data['description'];
        $this->features = $data['features'];
        $this->rating = (float) $data['rating'];
        $this->reviews = (int) $data['reviews'];
        $this->images = is_array($data['images'] ?? null) ? $data['images'] : [];
        $this->image = (string) ($data['image'] ?? ($this->images[0]['path'] ?? ''));
        $this->variants = is_array($data['variants'] ?? null) ? $data['variants'] : [];
        $this->sellerId = isset($data['seller_id']) ? (int) $data['seller_id'] : null;
    }

    /** Devuelve el identificador de la primera variante para formularios HTML. */
    public function getDefaultVariantId(): string
    {
        return isset($this->variants[0]['id']) ? (string) $this->variants[0]['id'] : 'default';
    }
}
