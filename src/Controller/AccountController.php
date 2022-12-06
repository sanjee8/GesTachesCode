<?php

namespace App\Controller;

use App\Form\NewPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/account", name="account")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $user = $this->getUser();

        $pw_msg = null;

        $form_password = $this->createForm(NewPasswordType::class, $user);
        $form_password->handleRequest($request);


        if($form_password->isSubmitted() && $form_password->isValid()) {

            if($userPasswordHasher->isPasswordValid($user, $user->getOldPassword())) {



            } else {
                $pw_msg = array(
                    "css" => "danger",
                    "msg" => "Votre mot de passe actuel n'est pas correcte !"
                );
            }

        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'form_password' => $form_password->createView(),
            'password_msg' => $pw_msg
        ]);
    }
}
