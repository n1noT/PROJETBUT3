<?php

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Events; 
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormError;


class GestionController extends AbstractController
{
    private $eventRepository;

    public function __construct(EventsRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    #[Route('/gestion', name: 'app_gestion')]
    public function index(): Response
    {
        return $this->render('gestion/index.html.twig', [
            'controller_name' => 'GestionController',
        ]);
    }

    #[Route('/gestion/events', name: 'app_gestion_events')]
    public function events(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $events = $this->eventRepository->findAll();

        return $this->render('gestion/events.html.twig', [
            'events' => $events,
        ]);

    }

    #[Route('/gestion/events/input/{id?}', name: 'app_gestion_events_input')]
    public function event(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $request->get('id');
        if($id && ctype_digit($id)) {
            $event = $this->eventRepository->find($id);
            if(!$event){
                $event = new Events();
            }
        } else {
            $event = new Events();
        }

        $form = $this->createFormBuilder($event)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateStart', DateType::class)
            ->add('dateEnd', DateType::class)
            ->add('visibility', CheckboxType::class,
            ['required' => false])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateStart = $form->get('dateStart')->getData();
            $dateEnd = $form->get('dateEnd')->getData();
            if($dateStart > $dateEnd){
                $form->get('dateEnd')->addError(new FormError('The end date cannot be earlier than the start date.'));
            }
            else{
                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('app_gestion_events');
            }
        }

        return $this->render('gestion/eventForm.html.twig', [
            'event' => $event,
            'eventForm' => $form,
        ]);
        
    }
}
