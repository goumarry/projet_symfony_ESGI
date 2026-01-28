<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookFrontendController extends AbstractController
{
    #[Route('/books', name: 'app_books_catalog')]
    public function catalog(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('book_frontend/index.html.twig');
    }

    #[Route('/admin/books', name: 'app_books_admin')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('book_frontend/admin.html.twig');
    }
}