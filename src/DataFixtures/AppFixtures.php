<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Pictures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        //Use faker
        $faker = Factory::create('fr_FR');

        /**
         * create a fake user with faker
         */
        $user = new User();

        $user
            ->setEmail('user@test.com')
            ->setFirstname($faker->firstName())
            ->setName($faker->name())
            ->setAbout($faker->text())
            ->setFacebook('facebook');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);

        /**
         * creattion de 10 pictures
         */
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category
                ->setName($faker->word())
                ->setDescription($faker->words(10, true))
                ->setSlug($faker->slug());
            $manager->persist($category);

            /**
             * creation 2pictures/Category
             */
            for ($j = 1; $j <= 2; $j++) {
                $picture = new Pictures();
                $picture
                    ->setName($faker->words(3, true))
                    ->setDateUpload($faker->dateTimeBetween('-6 month', 'now'))
                    ->setDescription($faker->text())
                    ->setSlug($faker->slug())
                    ->setFile('/img/portfolio/portfolio-' . $i * $j . '.jpg')
                    ->setUser($user)
                    ->addCategory($category);
                $manager->persist($picture);
            }
        }

        $manager->flush();
    }
}
