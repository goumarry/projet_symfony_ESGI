<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LoginAttempt;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $users = $userRepository->findAll();

        $attempts = $em->getRepository(LoginAttempt::class)->findBy([], ['happenedAt' => 'DESC'], 10);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'attempts' => $attempts,
        ]);
    }
}
