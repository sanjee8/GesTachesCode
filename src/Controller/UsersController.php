<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(EntityManagerInterface $manager): Response
    {

        $users = $manager->getRepository(User::class)->findAll();

        $editable = null;

        if($this->isGranted('ROLE_ADMIN')) {
           $editable = true;
        }


        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $users,
            'editable' => $editable
        ]);
    }

    /**
     * @Route("/users/del/{id}", name="users_delete")
     */
    public function remove(int $id, EntityManagerInterface $manager) : Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("users");
        }

        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("users");
        }

        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute("users");
    }

    /**
     * @Route("/users/admin/{id}", name="users_set_admin")
     */
    public function setAdmin(int $id, EntityManagerInterface $manager) : Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("users");
        }

        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("users");
        }

        $user->setRoles(array('ROLE_ADMIN'));

        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute("users");
    }

    /**
     * @Route("/users/rem-admin/{id}", name="users_remove_admin")
     */
    public function removeAdmin(int $id, EntityManagerInterface $manager) : Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("users");
        }

        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $id));
        if($user == null) {
            return $this->redirectToRoute("users");
        }

        $user->setRoles(array(''));

        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute("users");
    }


}
