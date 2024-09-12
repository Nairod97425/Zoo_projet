<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\CompteRendu;
use App\Entity\Habitat;
use App\Entity\Animal;
use App\Entity\Service;
use App\Repository\CompteRenduRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     *
     * @Route("/admin", name="admin_dashboard")
     */
    #[Route('/admin', name: 'admin_dashboard')]
    public function index()
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * Crée un nouvel utilisateur (employé ou vétérinaire).
     *
     * @Route("/admin/user/new", name="admin_create_user")
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
            $user->setPassword($hashedPassword);

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
     *
     * @Route("/admin/users", name="admin_users")
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
     *
     * @Route("/admin/user/edit/{id}", name="admin_edit_user")
     */
    #[Route('/admin/user/edit/{id}', name: 'admin_edit_user')]
    public function editUser($id, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez si un nouveau mot de passe est défini, sinon ne pas changer l'ancien mot de passe
            if ($user->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
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
     *
     * @Route("/admin/user/delete/{id}", name="admin_delete_user")
     */
    #[Route('/admin/user/delete/{id}', name: 'admin_delete_user')]
    public function deleteUser($id, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

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
     *
     * @Route("/admin/comptes-rendus", name="admin_comptes_rendus")
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
     * Gère les habitats du zoo.
     *
     * @Route("/admin/habitats", name="admin_habitats")
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
     *
     * @Route("/admin/animaux", name="admin_animals")
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
     *
     * @Route("/admin/services", name="admin_services")
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
}
