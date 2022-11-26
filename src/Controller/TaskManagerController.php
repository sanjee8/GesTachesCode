<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\UpdateTaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskManagerController extends AbstractController
{
    /**
     * @Route("/task/{id}", name="app_task_manager")
     */
    public function index(int $id, EntityManagerInterface $manager, Request $request): Response
    {

        $task = $manager->getRepository(Task::class)->findOneBy(array('id' => $id));
        if($task == null) {
            return $this->redirectToRoute("task", array('m' => "not_found"));
        }


        $form_task = $this->createForm(UpdateTaskType::class, $task);
        $form_task->handleRequest($request);

        if($form_task->isSubmitted() && $form_task->isValid()) {
            $manager->persist($task);
            $manager->flush();
        }

        $task_status = array(
            "msg" => "Tâche en cours",
            "css" => "info"
        );
        $pourcent = $task->getPourcent();
        if($pourcent == 0) {
            $task_status = array(
                "msg" => "Tâche en attente",
                "css" => "secondary"
            );
        } elseif ($pourcent >= 100) {
            $task_status = array(
                "msg" => "Tâche terminée",
                "css" => "success"
            );
        }




        return $this->render('task/infos.html.twig', [
            'controller_name' => 'TaskManagerController',
            'task' => $task,
            'task_status' => $task_status,
            'form_update' => $form_task->createView()
        ]);
    }
}
