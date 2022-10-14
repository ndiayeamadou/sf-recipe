<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /** using fakerphp */
    private Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->faker = Factory::create();
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $ingredients = [];
        for($i = 0; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            //$ingredient->setName('Ingredient # '.$i)
            //            ->setPrice(rand(0,200));
            /** using fakerphp */
            $ingredient->setName($this->faker->word())->setPrice(rand(0, 200));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes
        for($i=0; $i<=22; $i++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())->setPrice(rand(0, 1000))
                    ->setTime(mt_rand(0, 1) == 0 ? mt_rand(0, 1440) : null)
                    ->setNbpers(rand(1, 50))->setHard(rand(1, 5))->setDescription($this->faker->text(255))
                    ->setIsFavorite(mt_rand(0, 1) == 0 ? false : true);

            for($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recipe);
        }

        // USERS
        $plaintextPassword = "passer";
        for($u = 0; $u <= 5; $u++) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setFullName($this->faker->name())
                    ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                    ->setEmail($this->faker->email)->setPassword($hashedPassword)
                    ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();

    }
}
