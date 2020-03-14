<?php


namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Usuario;


class UsuarioFixtures extends  Fixture
{

    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

public function load(ObjectManager $manager)
{
    $user1 = new Usuario();
    $user1->setEmail('comercial@app.com');
    $user1->setPassword($this->passwordEncoder->encodePassword(
     $user1,
     'comercial123'
    ));
    $user1->setRoles(['ROLE_COMERCIAL']);
    $user1->setLocale('es');

    $manager->persist($user1);

    $user2 = new Usuario();
    $user2->setEmail('admin@app.com');
    $user2->setPassword($this->passwordEncoder->encodePassword(
        $user2,
        'admin123'
    ));
    $user2->setRoles(['ROLE_ADMINISTRADOR']);
    $user2->setLocale('en');

    $manager->persist($user2);

    $user3 = new Usuario();
    $user3->setEmail('jefeproyecto@app.com');
    $user3->setPassword($this->passwordEncoder->encodePassword(
        $user3,
        'jefe123'
    ));
    $user3->setRoles(['ROLE_JEFEPROYECTO']);
    $user3->setLocale('en');

    $manager->persist($user3);

    $user4 = new Usuario();
    $user4->setEmail('empleado@app.com');
    $user4->setPassword($this->passwordEncoder->encodePassword(
        $user4,
        'emp123'
    ));
    $user4->setRoles(['ROLE_TECNICO']);
    $user4->setLocale('es');

    $manager->persist($user4);
    $manager->flush();

}

}