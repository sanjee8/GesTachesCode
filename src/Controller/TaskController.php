<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index(EntityManagerInterface $manager): Response
    {

        $msg = null;
        $tasks = $manager->getRepository(Task::class)->findAll();

        if(isset($_GET["m"])) {
            $msg = trim($_GET['m']);
            if($msg == "task_created") {
                $msg = "La tâche a bien été crée avec succès !";
            }
        }


        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/task/new", name="new_task")
     */
    public function create(EntityManagerInterface $manager, Request $request): Response
    {

        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);




        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $task->setCreateBy($this->getUser());




            $brut_text = explode(",", $task->getCollabsInput());

            $user_id = $this->getUser()->getId();


            foreach ($brut_text as $collab) {

                if($user_id == intval($collab)) break;



                $user = $manager->getRepository(User::class)->findOneBy(array('id' => $collab));
                $task->addCollaborator($user);

            }


            $task->setStatus("En cours");
            $task->setDateCreated(new DateTime());

            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute("task", array('m' => "task_created"));


        }








        return $this->render('task/new.html.twig', [
            'form' => $form->createView()
        ]);
    }





}
