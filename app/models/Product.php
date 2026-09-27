<?php
/** Modelo de datos de un producto del catálogo. */
class Product
{
    public string $id;
    public string $name;
    public string $category;
    public float $price;
    public string $description;
    public array $features;
    public float $rating;
    public int $reviews;
    public string $image;

    public function __construct(array $data)
    {
        $this->id = (string) $data['id'];
        $this->name = (string) $data['name'];
        $this->category = (string) $data['category'];
        $this->price = (float) $data['price'];
        $this->description = (string) $data['description'];
        $this->features = $data['features'];
        $this->rating = (float) $data['rating'];
        $this->reviews = (int) $data['reviews'];
        $this->image = (string) ($data['image'] ?? '');
    }
}
