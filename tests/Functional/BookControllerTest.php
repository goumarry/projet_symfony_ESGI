<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    private function createAndLoginAdmin($client)
    {
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
        $userRepo = $container->get(UserRepository::class);

        $user = $userRepo->findOneBy(['email' => 'test@api.com']);

        if (!$user) {
            $user = new User();
            $user->setEmail('test@api.com');
            $user->setPassword('$2y$13$PyX...');
            $user->setRoles(['ROLE_ADMIN']);
            $user->setIsVerified(true);
            $em->persist($user);
            $em->flush();
        }

        $client->loginUser($user);
    }

    public function testAccessDeniedForAnonymous(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/books', [], [], ['CONTENT_TYPE' => 'application/json'], '{}');

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [302, 401]));
    }


    public function testCreateBookSuccess(): void
    {
        $client = static::createClient();
        $this->createAndLoginAdmin($client);

        $data = [
            'titre' => 'Livre de Test',
            'auteur' => 'Victor Hugo',
            'isbn' => '978-2070413119',
            'datePublication' => '1862-01-01',
            'description' => 'Les Misérables'
        ];

        $client->request(
            'POST',
            '/api/books',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(201);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Livre de Test', $responseData['titre']);
        $this->assertEquals('Victor Hugo', $responseData['auteur']);
    }

    public function testCreateBookValidationError(): void
    {
        $client = static::createClient();
        $this->createAndLoginAdmin($client); // <--- ON SE CONNECTE

        $data = [
            'titre' => '', // Vide !
            'auteur' => 'Inconnu',
            'isbn' => 'invalid-isbn',
            'datePublication' => '2022-01-01'
        ];

        $client->request(
            'POST',
            '/api/books',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('errors', $client->getResponse()->getContent());
    }

    public function testDeleteBookNotFound(): void
    {
        $client = static::createClient();
        $this->createAndLoginAdmin($client);

        $client->request('DELETE', '/api/books/99999');

        $this->assertResponseStatusCodeSame(404);
        $this->assertStringContainsString('Impossible de supprimer', $client->getResponse()->getContent());
    }
}