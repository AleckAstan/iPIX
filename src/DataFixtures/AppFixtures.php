<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Pictures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Use faker
        $faker = Factory::create('fr_FR');

        /**
         * create a fake user with faker
         */
        $user = new User();

        $user
            ->setEmail('user@email.com')
            ->setFirstname($faker->firstName())
            ->setName($faker->name())
            ->setAbout($faker->text())
            ->setFacebook('facebook')
            ->setPassword('password');
        $manager->persist($user);

        /**
         * creattion de 10 pictures
         */
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category
                ->setName($faker->word())
                ->setDescription($faker->words(10, true))
                ->setSlug($faker->slug());
            $manager->persist($category);

            /**
             * creation 2pictures/Category
             */
            for ($j = 0; $j < 2; $j++) {
                $picture = new Pictures();
                $picture
                    ->setName($faker->words(3, true))
                    ->setDateUpload($faker->dateTimeBetween('-6 month', 'now'))
                    ->setDescription($faker->text())
                    ->setSlug($faker->slug())
                    ->setFile('/img/portfolio/portfolio-1.jpg')
                    ->setUser($user)
                    ->addCategory($category);
                $manager->persist($picture);
            }
        }

        $manager->flush();
    }
}
