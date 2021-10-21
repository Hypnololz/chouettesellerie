<?php

namespace App\DataFixtures;

use App\Entity\News;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProductFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');


        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product
                ->setName($faker->sentence('5'))
                ->setStock($faker->numberBetween(5,100))
                ->setPhoto($faker->numberBetween(1,26).'.jpg')
                ->setReference("1337")
                ->setPrice($faker->numberBetween(4,500))
                ->setDescription($faker->paragraph(4))
                ->setBrand($faker->domainWord)
                ->setGammes($this->getReference('chien'));
            $manager->persist($product);
        }
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product
                ->setName($faker->sentence('5'))
                ->setStock($faker->numberBetween(5,100))
                ->setPhoto($faker->numberBetween(1,26).'.jpg')
                ->setReference("1337")
                ->setPrice($faker->numberBetween(4,500))
                ->setDescription($faker->paragraph(4))
                ->setBrand($faker->domainWord)
                ->setGammes($this->getReference('cheval'));
            $manager->persist($product);
        }

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product
                ->setName($faker->sentence('5'))
                ->setStock($faker->numberBetween(5,100))
                ->setPhoto($faker->numberBetween(1,26).'.jpg')
                ->setReference("1337")
                ->setPrice($faker->numberBetween(4,500))
                ->setDescription($faker->paragraph(4))
                ->setBrand($faker->domainWord)
                ->setGammes($this->getReference('soins'));
            $manager->persist($product);
        }

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product
                ->setName($faker->sentence('5'))
                ->setStock($faker->numberBetween(5,100))
                ->setPhoto($faker->numberBetween(1,26).'.jpg')
                ->setReference("1337")
                ->setPrice($faker->numberBetween(4,500))
                ->setDescription($faker->paragraph(4))
                ->setBrand($faker->domainWord)
                ->setGammes($this->getReference('cavalier'));
            $manager->persist($product);
        }



        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }
}
