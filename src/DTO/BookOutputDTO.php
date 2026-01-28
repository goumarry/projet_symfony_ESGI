<?php


namespace App\DTO;

class BookOutputDTO
{
    #[OA\Property(description: "ID unique du livre", example: 1)]
    public ?int $id = null;
    public string $titre;
    public string $auteur;
    public string $isbn;
    public ?string $description;
    public string $datePublication;
}