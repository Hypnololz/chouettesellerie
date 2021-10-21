<?php

namespace App\DataFixtures;

use App\Entity\Gammes;
use App\Entity\News;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        //creation compte admin
        $admin = new User();

        $admin
            ->setEmail('a@a.a')
            ->setFirstName('chouette')
            ->setName('chouetteAdmin')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword(
                $this->encoder->hashPassword($admin, 'a')
            )
        ;
        $manager->persist($admin);

        for ($i = 0; $i < 50; $i++){
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setFirstName( $faker->firstName)
                ->setName($faker->userName )
                ->setPassword(
                    $this->encoder->hashPassword($user, $faker->password()))
            ;
            $manager->persist($user);
        }

        for ($i = 0; $i < 50; $i++) {
            $news = new News();
            $news
                ->setTitle($faker->sentence('5'))
                ->setAuthor($admin)
                ->setPhoto($faker->numberBetween(1,26).'.jpg')
                ->setContent($faker->paragraph(15));
            $manager->persist($news);
        }

        //creation fixture gammes

        $chien = new Gammes();
        $chien->setName('chien');
        $manager->persist($chien);
        $this->addReference('chien', $chien);


        $cheval = new Gammes();
        $cheval->setName('cheval');
        $manager->persist($cheval);
        $this->addReference('cheval', $cheval);


        $soins = new Gammes();
        $soins->setName('soins');
        $manager->persist($soins);
        $this->addReference('soins', $soins);


        $cavalier = new Gammes();
        $cavalier->setName('cavalier');
        $manager->persist($cavalier);
        $this->addReference('cavalier', $cavalier);

        $manager->flush();

    }
}
