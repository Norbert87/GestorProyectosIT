<?php


namespace App\DataFixtures;

use App\Entity\Caracteristica;
use App\Entity\CaracteristicaTraduccion;
use App\Entity\TipoAplicacionTraduccion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\TipoAplicacion;


class TipoCaracteristicasAplicacionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tipo1 = new TipoAplicacion();
        $tipo1->setNombre('Web');
        $tipo1->setPreciobase('1000');

        $manager->persist($tipo1);

        $tradtipo1= new TipoAplicacionTraduccion();
        $tradtipo1->setLocale('en');
        $tradtipo1->setNombre('Website');
        $tradtipo1->setTipoAplicacion($tipo1);
        $manager->persist($tradtipo1);

        $tipo2 = new TipoAplicacion();
        $tipo2->setNombre('Escritorio');
        $tipo2->setPreciobase('1500');

        $tradtipo2= new TipoAplicacionTraduccion();
        $tradtipo2->setLocale('en');
        $tradtipo2->setNombre('Escritorio');
        $tradtipo2->setTipoAplicacion($tipo2);
        $manager->persist($tradtipo2);

        $manager->persist($tipo2);

        $tipo3 = new TipoAplicacion();
        $tipo3->setNombre('Móvil');
        $tipo3->setPreciobase('50');

        $manager->persist($tipo3);

        $tradtipo3= new TipoAplicacionTraduccion();
        $tradtipo3->setLocale('en');
        $tradtipo3->setNombre('Phone');
        $tradtipo3->setTipoAplicacion($tipo3);
        $manager->persist($tradtipo3);

        $carac1 = new Caracteristica();
        $carac1->setNombre('Gestion de Usuarios');
        $carac1->setPreciobase('1000');
        $carac1->setTipoaplicacion($tipo1);
        $manager->persist($carac1);

        $tradcarac1= new CaracteristicaTraduccion();
        $tradcarac1->setLocale('en');
        $tradcarac1->setNombre("User's crud");
        $tradcarac1->setCaracteristica($carac1);
        $manager->persist($tradcarac1);

        $carac2 = new Caracteristica();
        $carac2->setNombre('Envio de emails');
        $carac2->setPreciobase('500');
        $carac2->setTipoaplicacion($tipo1);
        $manager->persist($carac2);

        $tradcarac2= new CaracteristicaTraduccion();
        $tradcarac2->setLocale('en');
        $tradcarac2->setNombre("Emails delivery");
        $tradcarac2->setCaracteristica($carac2);
        $manager->persist($tradcarac2);

        $carac3 = new Caracteristica();
        $carac3->setNombre('RSS');
        $carac3->setPreciobase('500');
        $carac3->setTipoaplicacion($tipo1);
        $manager->persist($carac3);

        $tradcarac3= new CaracteristicaTraduccion();
        $tradcarac3->setLocale('en');
        $tradcarac3->setNombre("RSS");
        $tradcarac3->setCaracteristica($carac3);
        $manager->persist($tradcarac3);

        $carac4 = new Caracteristica();
        $carac4->setNombre('Blog');
        $carac4->setPreciobase('1000');
        $carac4->setTipoaplicacion($tipo1);
        $manager->persist($carac4);

        $tradcarac4= new CaracteristicaTraduccion();
        $tradcarac4->setLocale('en');
        $tradcarac4->setNombre("Blog");
        $tradcarac4->setCaracteristica($carac4);
        $manager->persist($tradcarac4);

        $carac5 = new Caracteristica();
        $carac5->setNombre('Landing');
        $carac5->setPreciobase('1000');
        $carac5->setTipoaplicacion($tipo1);
        $manager->persist($carac5);

        $tradcarac5= new CaracteristicaTraduccion();
        $tradcarac5->setLocale('en');
        $tradcarac5->setNombre("Landing");
        $tradcarac5->setCaracteristica($carac5);
        $manager->persist($tradcarac5);

        $carac6 = new Caracteristica();
        $carac6->setNombre('Panel administracion');
        $carac6->setPreciobase('1000');
        $carac6->setTipoaplicacion($tipo1);
        $manager->persist($carac6);

        $tradcarac6= new CaracteristicaTraduccion();
        $tradcarac6->setLocale('en');
        $tradcarac6->setNombre("Administrator Panel");
        $tradcarac6->setCaracteristica($carac6);
        $manager->persist($tradcarac6);

        $carac7 = new Caracteristica();
        $carac7->setNombre('Base de datos local');
        $carac7->setPreciobase('1000');
        $carac7->setTipoaplicacion($tipo2);
        $manager->persist($carac7);

        $tradcarac7= new CaracteristicaTraduccion();
        $tradcarac7->setLocale('en');
        $tradcarac7->setNombre("Data base local");
        $tradcarac7->setCaracteristica($carac7);
        $manager->persist($tradcarac7);

        $carac8 = new Caracteristica();
        $carac8->setNombre('Base de datos remota');
        $carac8->setPreciobase('1000');
        $carac8->setTipoaplicacion($tipo2);
        $manager->persist($carac8);

        $tradcarac8= new CaracteristicaTraduccion();
        $tradcarac8->setLocale('en');
        $tradcarac8->setNombre("Remote Data base");
        $tradcarac8->setCaracteristica($carac8);
        $manager->persist($tradcarac8);

        $carac9 = new Caracteristica();
        $carac9->setNombre('Gestion de Usuarios');
        $carac9->setPreciobase('1000');
        $carac9->setTipoaplicacion($tipo2);
        $manager->persist($carac9);

        $tradcarac9= new CaracteristicaTraduccion();
        $tradcarac9->setLocale('en');
        $tradcarac9->setNombre("User's crud");
        $tradcarac9->setCaracteristica($carac9);
        $manager->persist($tradcarac9);

        $carac10 = new Caracteristica();
        $carac10->setNombre('Panel administracion');
        $carac10->setPreciobase('1000');
        $carac10->setTipoaplicacion($tipo2);
        $manager->persist($carac10);

        $tradcarac10= new CaracteristicaTraduccion();
        $tradcarac10->setLocale('en');
        $tradcarac10->setNombre("Administrator panel");
        $tradcarac10->setCaracteristica($carac10);
        $manager->persist($tradcarac10);

        $carac11 = new Caracteristica();
        $carac11->setNombre('Panel administracion');
        $carac11->setPreciobase('1000');
        $carac11->setTipoaplicacion($tipo3);
        $manager->persist($carac11);

        $tradcarac11= new CaracteristicaTraduccion();
        $tradcarac11->setLocale('en');
        $tradcarac11->setNombre("Administrator panel");
        $tradcarac11->setCaracteristica($carac11);
        $manager->persist($tradcarac11);

        $carac12 = new Caracteristica();
        $carac12->setNombre('Gestion de Usuarios');
        $carac12->setPreciobase('1000');
        $carac12->setTipoaplicacion($tipo3);
        $manager->persist($carac12);

        $tradcarac12= new CaracteristicaTraduccion();
        $tradcarac12->setLocale('en');
        $tradcarac12->setNombre("User's crud");
        $tradcarac12->setCaracteristica($carac12);
        $manager->persist($tradcarac12);

        $carac13 = new Caracteristica();
        $carac13->setNombre('IOS');
        $carac13->setPreciobase('1000');
        $carac13->setTipoaplicacion($tipo3);
        $manager->persist($carac13);

        $tradcarac13= new CaracteristicaTraduccion();
        $tradcarac13->setLocale('en');
        $tradcarac13->setNombre("IOS");
        $tradcarac13->setCaracteristica($carac13);
        $manager->persist($tradcarac13);


        $carac14 = new Caracteristica();
        $carac14->setNombre('Android');
        $carac14->setPreciobase('1000');
        $carac14->setTipoaplicacion($tipo3);
        $manager->persist($carac14);

        $tradcarac14= new CaracteristicaTraduccion();
        $tradcarac14->setLocale('en');
        $tradcarac14->setNombre("Android");
        $tradcarac14->setCaracteristica($carac14);
        $manager->persist($tradcarac14);

        $carac15 = new Caracteristica();
        $carac15->setNombre('Android/IOS');
        $carac15->setPreciobase('1000');
        $carac15->setTipoaplicacion($tipo3);
        $manager->persist($carac15);

        $tradcarac15= new CaracteristicaTraduccion();
        $tradcarac15->setLocale('en');
        $tradcarac15->setNombre("Android/IOS");
        $tradcarac15->setCaracteristica($carac15);
        $manager->persist($tradcarac15);

        $carac16 = new Caracteristica();
        $carac16->setNombre('Notificaciones Push');
        $carac16->setPreciobase('1000');
        $carac16->setTipoaplicacion($tipo3);
        $manager->persist($carac16);

        $tradcarac16= new CaracteristicaTraduccion();
        $tradcarac16->setLocale('en');
        $tradcarac16->setNombre("Push notifications");
        $tradcarac16->setCaracteristica($carac16);
        $manager->persist($tradcarac16);

        $manager->flush();


    }
}