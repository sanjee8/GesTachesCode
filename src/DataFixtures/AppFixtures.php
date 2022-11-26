<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);


        for ($i = 0; $i < 20; $i++) {

            $date = new DateTime();
            $date->format("d/m/Y H:i");



            $task = new Task();
            $task->setName("tache " . $i)
                ->setDateCreated($date)
                ->setStatus("En cours")
                ->setDescription("blabl sdfg dfgdfg dfg dfg ");

            $manager->persist($task);

        }


        $manager->flush();
    }
}
