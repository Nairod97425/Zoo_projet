<?php
// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Habitat;
use App\Form\HabitatType;
use App\Form\UserType;
use App\Repository\HabitatRepository;
use App\Repository\UserRepository;
use App\Entity\Animal;
use App\Form\AnimalType;
// use App\Repository\AnimalRepository;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminController extends AbstractController
{

    // private function handleFileUpload(UploadedFile $file): string
    // {
    //     $filename = uniqid() . '.' . $file->guessExtension();
    //     $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/images', $filename);
    //     return $filename;
    // }

    private EntityManagerInterface $entityManager;

    // Injection de l'EntityManager dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        // Logique pour le tableau de bord de l'administrateur
        return $this->render('admin/dashboard.html.twig');
    }

    //Gestion des nouveaux utiliisateur
    #[Route(path: '/create_user', name: 'admin_create_user')]
    public function createUser(Request $request): Response
    {
        // Créer un nouvel objet User
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister le nouvel utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('admin_list_users');
        }

        return $this->render('admin/user/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/users', name: 'admin_list_users')]
    public function listUsers(): Response
    {
        // Récupérer la liste des utilisateurs depuis la base de données
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user/list_users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route(path: '/admin/user/edit/{id}', name: 'admin_edit_user')]
    public function editUser(int $id, UserRepository $userRepository, Request $request): Response
    {
        // Récupérer l'utilisateur par ID via le repository
        $user = $userRepository->find($id);

         // Si l'utilisateur n'existe pas, redirigez ou gérez l'erreur
         if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Créer le formulaire pour éditer l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'utilisateur
            $this->entityManager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('admin_list_users');
        }

        return $this->render('admin/user/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route(path: '/admin/user/delete/{id}', name: 'admin_delete_user')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        // Récupérer l'utilisateur par son ID
        $user = $userRepository->find($id);
        
       // Vérifier si l'utilisateur existe
       if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Supprimer l'utilisateur de la base de données
    $entityManager->remove($user);
    $entityManager->flush();

    // Rediriger après la suppression
    $this->addFlash('success', 'Utilisateur supprimé avec succès');
    return $this->redirectToRoute('admin_list_user');  // Remplacez par la route vers la liste des utilisateurs
}

    //Gestion des Habitats

    #[Route(path: '/admin/habitats', name: 'admin_habitats')]
    public function index(HabitatRepository $habitatRepository): Response
    {
        $habitats = $habitatRepository->findAll();

        return $this->render('admin/habitat/manage_habitats.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    #[Route(path: '/admin/habitat/create', name: 'admin_create_habitat')]
    public function create_habitat(Request $request): Response
    {
        $habitat = new Habitat();
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le ou les fichiers téléchargés
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->handleFileUpload($file);
                        $filenames[] = $filename;
                    }
                }
                $habitat->setImages($filenames);
            }

            // Persister l'entité Habitat
            $this->entityManager->persist($habitat);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'habitat a été créé avec succès.');
            return $this->redirectToRoute('admin_habitats');
        }

        return $this->render('admin/habitat/manage_habitats.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour gérer l'upload du fichier
    private function handleFileUpload(UploadedFile $file): string
    {
        $uploadsDirectory = $this->getParameter('uploads_directory'); // Vous pouvez définir ce paramètre dans config/services.yaml

        // Générer un nom unique pour le fichier
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            // Déplacer le fichier téléchargé dans le répertoire de destination
            $file->move(
                $uploadsDirectory,  // Dossier où enregistrer l'image
                $filename
            );
        } catch (IOExceptionInterface $exception) {
            // Gérer l'exception si un problème survient lors de l'upload
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
            throw $exception; // Vous pouvez aussi choisir de gérer l'exception autrement
        }

        return $filename;
    }



    #[Route(path: '/admin/habitat/edit/{id}', name: 'admin_edit_habitat')]
    public function edit_habitat(Habitat $habitat, Request $request): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        // $habitat = $habitatRepository->find($id);
        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé.');
        }

        // Logique pour modifier l'habitat
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/edit.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    #[Route(path: '/admin/habitat/delete/{id}', name: 'admin_delete_habitat', methods: ['POST'])]
    public function delete_habitat(int $id, HabitatRepository $habitatRepository): Response
    {
        $habitat = $habitatRepository->find($id);
        if ($habitat) {
            $entityManager = $this->entityManager;
            $entityManager->remove($habitat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_habitats');
    }

    //Gestion des Animaux

    #[Route('/admin/animal/create', name: 'admin_create_animal', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        // Logique pour ajouter un nouvel animal
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($animal);
            $this->entityManager->flush();

            $this->addFlash('success', 'Animal créé avec succès.');
            return $this->redirectToRoute('admin_manage_animals');
        }

        return $this->render('admin_animal/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/animal/edit/{id}', name: 'admin_edit_animal', methods: ['GET', 'POST'])]
    public function edit(Animal $animal, Request $request): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Animal modifié avec succès.');

            return $this->redirectToRoute('animal_index');
        }

        return $this->render('admin_animal/edit.html.twig', [
            'form' => $form->createView(),
            'animal' => $animal,
        ]);
    }


    #[Route('/admin/animal/delete/{id}', name: 'admin_delete_animal', methods: ['POST'])]
    public function delete(Animal $animal, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($animal);
            $this->entityManager->flush();

            $this->addFlash('success', 'Animal supprimé avec succès.');
        }

        return $this->redirectToRoute('animal_index');
    }
}
