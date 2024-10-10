<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User; // Pour manipuler des objets "User"
use Symfony\Component\HttpFoundation\Request; // Permet d'accéder aux données postées
use Doctrine\ORM\EntityManagerInterface; // Permet d'enregistrer les objets dans la base de données
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Pour hacher les mots de passe
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class AdminController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);

    }

    #[Route('/admin/users/input/{id?}', name: 'app_admin_users_input')]
    public function user(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $request->get('id');
        if($id && ctype_digit($id)) {
            $user = $this->userRepository->find($id);
            if(!$user){
                $user = new User();
            }
        } else {
            $user = new User();
        }

        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Gestion' => 'ROLE_GESTION',
                    'Admin' => 'ROLE_ADMIN',
                    'User' => '',
                ],
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('name', TextType::class)
            ->add('firstName', TextType::class)
            ->add('active', CheckboxType::class, ['required' => false])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // This field is not mapped to the User entity
                'required' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $plainPassword = $form->get('plainPassword')->getData();
            if(!empty($plainPassword)){
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/userForm.html.twig', [
            'userForm' => $form,
        ]);
        
    }


}
