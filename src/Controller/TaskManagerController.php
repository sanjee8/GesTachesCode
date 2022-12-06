<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TaskCollabType;
use App\Form\UpdateTaskType;
use DateTime;
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

        /**
         * Check if Task exists
         */
        if($task == null) {
            return $this->redirectToRoute("task", array('m' => "not_found"));
        }

        /**
         * Check if user can access to task
         */
        if(!$this->isGranted('ROLE_ADMIN')) {
            $tasks_check = $manager->getRepository(Task::class)->findByTaskOf($this->getUser());
            if(!$tasks_check->contains($task)) {
                return $this->redirectToRoute("task", array('m' => "not_found"));
            }
        }

        $editable = null;

        $form_task = $this->createForm(UpdateTaskType::class, $task);
        $form_task->handleRequest($request);

        $form_collabs = $this->createForm(TaskCollabType::class, $task);
        $form_collabs->handleRequest($request);
        $collab_add_msg = null;

        $comment = new Comment();
        $form_comment = $this->createForm(CommentType::class, $comment);
        $form_comment->handleRequest($request);

        if($form_comment->isSubmitted() && $form_comment->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setDateOf(new DateTime());
            $comment->setTask($task);

            $manager->persist($comment);
            $manager->flush();

        }


        if($task->getCreateBy()->getId() == $this->getUser()->getId() || $this->isGranted('ROLE_ADMIN')) {
            $editable = true;

            if($form_task->isSubmitted() && $form_task->isValid()) {
                $manager->persist($task);
                $manager->flush();
            }




            if($form_collabs->isSubmitted() && $form_collabs->isValid()) {

                $collab_input = $task->getCollabsInput();
                $collab = $manager->getRepository(User::class)->findOneBy(array("email" => $collab_input));

                if($collab == null) {
                    $collab_add_msg = array(
                        "css" => "danger",
                        "msg" => "Collaborateur introuvable."
                    );
                }
                else if($collab->getId() == $task->getCreateby()->getId()) {
                    $collab_add_msg = array(
                        "css" => "danger",
                        "msg" => "Ce collaborateur a déjà été ajouté à cette tâche."
                    );
                } else {
                    if (!$task->getCollaborators()->contains($collab) ) {

                        $task->addCollaborator($collab);

                        $manager->persist($task);
                        $manager->flush();

                        $collab_add_msg = array(
                            "css" => "success",
                            "msg" => "Le collaborateur a bien été ajouté à cette tâche."
                        );

                    } else {

                        $collab_add_msg = array(
                            "css" => "danger",
                            "msg" => "Ce collaborateur a déjà été ajouté à cette tâche."
                        );

                    }
                }



            }

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
            'form_update_editable' => $editable,
            'form_update' => $form_task->createView(),
            'form_collabs' => $form_collabs->createView(),
            'form_collabs_msg' => $collab_add_msg,
            'form_comment' => $form_comment->createView()
        ]);
    }



    /**
     * @Route("/task/del/{id}/{collab}", name="task_delete_collab")
     */
    public function deleteCollab(int $id, int $collab, EntityManagerInterface $manager, Request $request): Response
    {

        $task = $manager->getRepository(Task::class)->findOneBy(array('id' => $id));
        if($task == null) {
            return $this->redirectToRoute("task");
        }

        /**
         * Check if user can access to task
         */
        if(!$this->isGranted('ROLE_ADMIN')) {
            $tasks_check = $manager->getRepository(Task::class)->findByTaskOf($this->getUser());
            if(!$tasks_check->contains($task)) {
                return $this->redirectToRoute("task");
            }
        }

        $user = $manager->getRepository(User::class)->findOneBy(array('id' => $collab));

        if (!$task->getCollaborators()->contains($user) ) {
            return $this->redirectToRoute("task");
        }


        $task->removeCollaborator($user);
        $manager->persist($task);
        $manager->flush();


        return $this->redirectToRoute("app_task_manager", array('id' => $id));

    }

}
