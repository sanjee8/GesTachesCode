<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class RegisterController extends AbstractController
{
    /**
     * @Route("/user/register", name = "register")
     */

    public function createUser(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response {

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setDateRegister(new DateTime());

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login');

        }


        return $this->render('log/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}