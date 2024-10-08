<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {

            $users = [];

            foreach ($userRepository->findAll() as $user) {
                $users[] = $user->getEmail();
            }

            var_dump($users);

            return $this->render('user/index.html.twig', [
                'controller_name' => 'UserController',
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
