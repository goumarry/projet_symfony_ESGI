<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/team/{name}', name: 'app_team_profile')]
    public function profile(string $name): Response
    {
        // Simulez des données de membre d'équipe
        $teamMembers = [
            'alice' => [
                'role' => 'Développeuse Senior',
                'description' => 'Passionnée par les architectures distribuées et le clean code.',
                'image' => 'https://via.placeholder.com/150/FF5733/FFFFFF?text=Alice'
            ],
            'bob' => [
                'role' => 'Chef de Projet',
                'description' => 'Expert en gestion agile, il assure la bonne marche des projets.',
                'image' => 'https://via.placeholder.com/150/33FF57/FFFFFF?text=Bob'
            ],
            'charlie' => [
                'role' => 'Designer UX/UI',
                'description' => 'Créateur d\'expériences utilisateur intuitives et esthétiques.',
                'image' => 'https://via.placeholder.com/150/3357FF/FFFFFF?text=Charlie'
            ],
        ];

        // Récupère les données du membre ou des valeurs par défaut si non trouvé
        $memberData = $teamMembers[strtolower($name)] ?? [
            'role' => 'Membre de l\'équipe',
            'description' => 'Description par défaut pour ce membre.',
            'image' => 'https://via.placeholder.com/150/CCCCCC/FFFFFF?text=Inconnu'
        ];

        return $this->render('team/profile.html.twig', [
            'name' => ucfirst($name), // Met la première lettre en majuscule pour l'affichage
            'role' => $memberData['role'],
            'description' => $memberData['description'],
            'image' => $memberData['image'],
        ]);
    }
}
