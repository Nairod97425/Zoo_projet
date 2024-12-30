<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultationController extends AbstractController
{
    #[Route('/consultations', name: 'consultation_index')]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findAll();

        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultations,
        ]);
    }

    #[Route('/consultations/new', name: 'consultation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VETERINAIRE');

        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('consultation_index');
        }

        return $this->render('consultation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/consultations/edit/{id}', name: 'consultation_edit')]
    public function edit(Consultation $consultation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VETERINAIRE');

        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('consultation_index');
        }

        return $this->render('consultation/edit.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/consultations/delete/{id}', name: 'consultation_delete', methods: ['POST'])]
    public function delete(Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VETERINAIRE');

        $entityManager->remove($consultation);
        $entityManager->flush();

        return $this->redirectToRoute('consultation_index');
    }
}