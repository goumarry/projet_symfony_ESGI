<?php


namespace App\Service;

use App\DTO\BookInputDTO;
use App\DTO\BookOutputDTO;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookService
{
    public function __construct(
        private BookRepository         $bookRepository,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @return BookOutputDTO[]
     */
    public function getAllBooks(): array
    {
        $books = $this->bookRepository->findAll();

        return array_map(fn($book) => $this->mapEntityToDto($book), $books);
    }

    public function getBookById(int $id): BookOutputDTO
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new NotFoundHttpException("Livre non trouvé avec l'id " . $id);
        }

        return $this->mapEntityToDto($book);
    }


    public function createBook(BookInputDTO $dto): BookOutputDTO
    {
        $book = new Book();
        $this->mapDtoToEntity($dto, $book);

        $this->em->persist($book);
        $this->em->flush();

        return $this->mapEntityToDto($book);
    }

    public function updateBook(int $id, BookInputDTO $dto): BookOutputDTO
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new NotFoundHttpException("Impossible de modifier : Livre introuvable.");
        }

        // Mise à jour des champs
        $this->mapDtoToEntity($dto, $book);

        $this->em->flush();

        return $this->mapEntityToDto($book);
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new NotFoundHttpException("Impossible de supprimer : Livre introuvable.");
        }

        $this->em->remove($book);
        $this->em->flush();
    }

    private function mapEntityToDto(Book $book): BookOutputDTO
    {
        $dto = new BookOutputDTO();
        $dto->id = $book->getId();
        $dto->titre = $book->getTitre();
        $dto->auteur = $book->getAuteur();
        $dto->isbn = $book->getIsbn();
        $dto->description = $book->getDescription();
        $dto->datePublication = $book->getDatePublication()->format('Y-m-d');

        return $dto;
    }

    private function mapDtoToEntity(BookInputDTO $dto, Book $book): void
    {
        $book->setTitre($dto->titre);
        $book->setAuteur($dto->auteur);
        $book->setIsbn($dto->isbn);
        $book->setDescription($dto->description);
        $book->setDatePublication(new \DateTimeImmutable($dto->datePublication));
    }
}