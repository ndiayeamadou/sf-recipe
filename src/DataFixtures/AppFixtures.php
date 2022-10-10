<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /** using fakerphp */
    private Generator $faker;
    public function __construct() {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        //$ingredient->setName('Ingredient # 1')->setPrice('15.2');
        for($i=0; $i<=50; $i++) {
            $ingredient = new Ingredient();
            //$ingredient->setName('Ingredient # '.$i)
            //            ->setPrice(rand(0,200));
            /** using fakerphp */
            $ingredient->setName($this->faker->word())->setPrice(rand(0,200));
            $manager->persist($ingredient);
        }

        $manager->flush();

    }
}
