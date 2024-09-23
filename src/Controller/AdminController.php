<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\CompteRenduType;
use App\Entity\CompteRendu;
use App\Entity\Habitat;
use App\Entity\Animal;
use App\Entity\Consultation;
use App\Entity\Service;
use App\Repository\ConsultationRenduRepository;
use App\Repository\CompteRenduRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * Dashboard de l'administrateur.
     */
    #[Route('/admin', name: 'admin_dashboard')]
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * Dashboard admin
     */
    // #[Route("/admin", name: "admin_dashboard")]
    // public function adminDashboard()
    // {
    //     $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Protection par rôle

    //     // Rendu de la vue d'administration
    //     return $this->render('admin/dashboard.html.twig');
    // }

    /**
     * Dashboard employer
     */
    #[Route("/employe", name: "employe_dashboard")]
    public function employeDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER'); // Protection par rôle

        return $this->render('employe/dashboard.html.twig');
    }

    /**
     * Dashboard vétérinaire
     */
    #[Route('/veterinaire', name: 'vetirinaire_dasboard')]
    public function veterinaireDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_VETERINAIRE'); // Protection par rôle

        return $this->render('veterinaire/dashboard.html.twig');
    }


    /**
     * Crée un nouvel utilisateur (employé ou vétérinaire).
     */
    #[Route('/admin/user/new', name: 'admin_create_user')]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setMot_de_passe($hashedPassword);

            // Persistance du nouvel utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Logique d'envoi d'email avec seulement le username
            // TODO: Implémentez l'envoi d'email ici

            $this->addFlash('success', 'Le compte a été créé avec succès. Un email a été envoyé à l\'utilisateur.');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Liste les utilisateurs (employés et vétérinaires).
     */
    #[Route('/admin/users', name: 'admin_users')]
    public function listUsers(EntityManagerInterface $entityManager)
    {
        // Récupère tous les utilisateurs (employés et vétérinaires)
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/list_users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Modifier un utilisateur (employé ou vétérinaire).
     */
    #[Route('/admin/user/edit/{id_admin}', name: 'admin_edit_user')]
    public function editUser($id_admin, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $entityManager->getRepository(User::class)->find($id_admin);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez si un nouveau mot de passe est défini, sinon ne pas changer l'ancien mot de passe
            if ($user->getMot_de_passe()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getMot_de_passe());
                $user->setMot_de_passe($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été mis à jour avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un utilisateur (employé ou vétérinaire).
     */
    #[Route('/admin/user/delete/{id_admin}', name: 'admin_delete_user')]
    public function deleteUser($id_admin, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($id_admin);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');

        return $this->redirectToRoute('admin_users');
    }

    /**
     * Affiche les comptes rendus vétérinaires avec des filtres.
     */
    #[Route('/admin/comptes-rendus', name: 'admin_comptes_rendus')]
    public function viewCompteRendus(CompteRenduRepository $compteRenduRepository, Request $request)
    {
        // Filtres par date et animal
        $filters = [
            'animal' => $request->query->get('animal'),
            'date' => $request->query->get('date')
        ];

        // Assurez-vous que la méthode findByFilters existe dans CompteRenduRepository
        $comptesRendus = $compteRenduRepository->findByFilters($filters);

        return $this->render('admin/comptes_rendus.html.twig', [
            'comptes_rendus' => $comptesRendus,
        ]);
    }

    /**
     * Crée un nouveau compte rendu vétérinaire.
     */
    #[Route('/admin/comptes-rendus/new', name: 'admin_create_compte_rendu')]
    public function createCompteRendu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compteRendu = new CompteRendu();
        $form = $this->createForm(CompteRenduType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compteRendu);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte rendu a été créé avec succès.');

            return $this->redirectToRoute('admin_comptes_rendus');
        }

        return $this->render('admin/create_compte_rendu.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Gère les habitats du zoo.
     */
    #[Route('/admin/habitats', name: 'admin_habitats')]
    public function manageHabitats(EntityManagerInterface $entityManager)
    {
        // Gestion des habitats (affichage, création, modification, suppression)
        $habitats = $entityManager->getRepository(Habitat::class)->findAll();

        return $this->render('admin/manage_habitats.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    /**
     * Gère les animaux du zoo.
     */
    #[Route('/admin/animaux', name: 'admin_animals')]
    public function manageAnimals(EntityManagerInterface $entityManager)
    {
        // Gestion des animaux (affichage, création, modification, suppression)
        $animaux = $entityManager->getRepository(Animal::class)->findAll();

        return $this->render('admin/manage_animals.html.twig', [
            'animaux' => $animaux,
        ]);
    }

    /**
     * Gère les services du zoo.
     */
    #[Route('/admin/services', name: 'admin_services')]
    public function manageServices(EntityManagerInterface $entityManager)
    {
        // Gestion des services du zoo
        $services = $entityManager->getRepository(Service::class)->findAll();

        return $this->render('admin/manage_services.html.twig', [
            'services' => $services,
        ]);
    }

    /**
     * Affiche les consultations avec des filtres.
     */
    #[Route('/admin/consultations', name: 'admin_consultations')]
    public function viewConsultations(ConsultationRenduRepository $consultationRepository, Request $request)
    {
        // Filtres par date et animal
        $filters = [
            'animal' => $request->query->get('animal'),
            'date' => $request->query->get('date')
        ];

        // Assurez-vous que la méthode findByFilters existe dans ConsultationRepository
        $consultations = $consultationRepository->findByFilters($filters);

        return $this->render('admin/consultations.html.twig', [
            'consultations' => $consultations,
        ]);
    }

    #[Route('/animaux/{id}', name: 'animal_show', methods: ['GET'])]
    public function show(Animal $animal): Response
    {
        // Get the images associated with this animal
        $images = $animal->getImages(); // This will return a collection of Image entities

        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
            'images' => $images,
        ]);
    }
}
