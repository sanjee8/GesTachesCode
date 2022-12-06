<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function number(EntityManagerInterface $manager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login',
            array('error' => "must_connected"));
        }

        if($this->isGranted('ROLE_ADMIN')) {
            $tasks = $manager->getRepository(Task::class)->findBy(array(), null, 10);
        } else {
            $tasks = $manager->getRepository(Task::class)->findByTaskOfLimit($this->getUser(), 10);
        }


        return $this->render('home.html.twig', [
            'tasks' => $tasks
        ]);
    }
}