<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        for($c = 0; $c <= 7; $c++)
        {
            $manager->persist(
            (new Contact())->setFullName(Factory::create()->name(21))->setEmail(Factory::create()->email())
            ->setSubject(Factory::create()->text(33))->setMessage(Factory::create()->paragraph(5))
            );
        }
        
        $manager->flush();
    }
}
