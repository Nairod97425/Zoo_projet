<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur administrateur
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('ADMIN1');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin_password'));
        $manager->persist($admin);

        // Créer un utilisateur vétérinaire
        $veterinaire = new User();
        $veterinaire->setEmail('veterinaire@example.com');
        $veterinaire->setRoles(['ROLE_VETERINAIRE']);
        $veterinaire->setPseudo('VETERINAIRE1');
        $veterinaire->setPassword($this->passwordHasher->hashPassword($veterinaire, 'veterinaire_password'));
        $manager->persist($veterinaire);

        // Créer un utilisateur employé
        $employe = new User();
        $employe->setEmail('employe@example.com');
        $employe->setRoles(['ROLE_EMPLOYE']);
        $employe->setPseudo('EMPLOYE1');
        $employe->setPassword($this->passwordHasher->hashPassword($employe, 'employe_password'));
        $manager->persist($employe);

        $manager->flush();
    }
}
