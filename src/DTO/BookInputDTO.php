<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class BookInputDTO
{
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(min: 3)]
    #[OA\Property(description: "Le titre du livre", example: "Le Petit Prince")]
    public ?string $titre = null;

    #[Assert\NotBlank(message: "L'auteur est obligatoire")]
    #[OA\Property(description: "Nom de l'auteur", example: "Antoine de Saint-Exupéry")]
    public ?string $auteur = null;

    #[Assert\NotBlank]
    #[Assert\Isbn(message: "Ce code ISBN n'est pas valide")]
    #[OA\Property(description: "Code ISBN-10 ou ISBN-13", example: "978-3-16-148410-0")]
    public ?string $isbn = null;

    #[Assert\NotBlank]
    #[OA\Property(description: "Date au format YYYY-MM-DD", type: "string", format: "date", example: "1943-04-06")]
    public ?string $datePublication = null;

    #[OA\Property(description: "Résumé du livre", example: "L'histoire d'un aviateur qui rencontre un petit garçon...")]
    public ?string $description = null;
}