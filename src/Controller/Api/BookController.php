<?php

namespace App\Controller\Api;

use App\DTO\BookInputDTO;
use App\DTO\BookOutputDTO;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/books')]
#[OA\Tag(name: 'Books')] // Regroupe toutes les routes sous l'étiquette "Books"
class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_books_list', methods: ['GET'])]
    #[OA\Get(summary: "Récupérer la liste des livres")]
    #[OA\Response(
        response: 200,
        description: "Liste des livres retournée avec succès",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: BookOutputDTO::class))
        )
    )]
    public function list(): JsonResponse
    {
        return $this->json($this->bookService->getAllBooks(), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_books_show', methods: ['GET'])]
    #[OA\Get(summary: "Récupérer un livre par son ID")]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "L'identifiant unique du livre",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: "Détails du livre",
        content: new OA\JsonContent(ref: new Model(type: BookOutputDTO::class))
    )]
    #[OA\Response(response: 404, description: "Livre non trouvé")]
    public function show(int $id): JsonResponse
    {
        $bookDto = $this->bookService->getBookById($id);
        return $this->json($bookDto, Response::HTTP_OK);
    }

    #[Route('', name: 'api_books_create', methods: ['POST'])]
    #[OA\Post(summary: "Créer un nouveau livre")]
    #[OA\RequestBody(
        description: "Données du livre à créer",
        required: true,
        content: new OA\JsonContent(ref: new Model(type: BookInputDTO::class))
    )]
    #[OA\Response(
        response: 201,
        description: "Livre créé avec succès",
        content: new OA\JsonContent(ref: new Model(type: BookOutputDTO::class))
    )]
    #[OA\Response(response: 400, description: "Données invalides (JSON mal formé)")]
    #[OA\Response(response: 422, description: "Erreur de validation (champs manquants)")]
    public function create(Request $request): JsonResponse
    {
        try {
            /** @var BookInputDTO $dto */
            $dto = $this->serializer->deserialize($request->getContent(), BookInputDTO::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'JSON invalide.'], Response::HTTP_BAD_REQUEST);
        }

        if ($response = $this->validateDto($dto)) {
            return $response;
        }

        $createdBook = $this->bookService->createBook($dto);
        return $this->json($createdBook, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_books_update', methods: ['PUT'])]
    #[OA\Put(summary: "Mettre à jour un livre existant")]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID du livre à modifier",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(ref: new Model(type: BookInputDTO::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Livre mis à jour",
        content: new OA\JsonContent(ref: new Model(type: BookOutputDTO::class))
    )]
    #[OA\Response(response: 404, description: "Livre non trouvé")]
    #[OA\Response(response: 422, description: "Erreur de validation")]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), BookInputDTO::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'JSON invalide.'], Response::HTTP_BAD_REQUEST);
        }

        if ($response = $this->validateDto($dto)) {
            return $response;
        }

        $updatedBook = $this->bookService->updateBook($id, $dto);
        return $this->json($updatedBook, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_books_delete', methods: ['DELETE'])]
    #[OA\Delete(summary: "Supprimer un livre")]
    #[OA\Parameter(
        name: "id",
        in: "path",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(response: 204, description: "Livre supprimé (aucune réponse)")]
    #[OA\Response(response: 404, description: "Livre non trouvé")]
    public function delete(int $id): JsonResponse
    {
        $this->bookService->deleteBook($id);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function validateDto($dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return null;
    }
}