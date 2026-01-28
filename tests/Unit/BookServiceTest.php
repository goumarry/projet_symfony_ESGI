<?php

namespace App\Tests\Unit;

use App\DTO\BookInputDTO;
use App\DTO\BookOutputDTO;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    public function testCreateBookReturnsOutputDto(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $service = new BookService($bookRepo, $em);

        $inputDto = new BookInputDTO();
        $inputDto->titre = "Test Unit";
        $inputDto->auteur = "Tester";
        $inputDto->isbn = "1234567890";
        $inputDto->datePublication = "2023-01-01";

        $result = $service->createBook($inputDto);

        $this->assertInstanceOf(BookOutputDTO::class, $result);
        $this->assertEquals("Test Unit", $result->titre);
    }
}