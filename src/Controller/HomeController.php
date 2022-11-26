<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function number(): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login',
            array('error' => "must_connected"));
        }
        $number = random_int(0, 100);

        return $this->render('home.html.twig', [
        ]);
    }
}