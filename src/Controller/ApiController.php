<?php
/****************************************
 Fichier : ApiController.php
 Auteur : Frederick Perazzelli-Delorme
 Fonctionnalité : Ceci est un api qui permet de faire des requête a la base de donnée.
 Date : 2022/04/21
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 Date 1 Nom 1 Description 1
 Date 2 Nom 2 Description 2
 ...
 =========================================================
 À FAIRE :
 Rajouter les ACCES sur les fonctions (ROLE_ADMIN, ROLE_)
****************************************/


namespace App\Controller;

header('Content-Type: application/json');

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use App\Controller\StatusController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;


class ApiController extends AbstractController
{
    /***************************************************************************************************
    *
    * UTILISATEUR
    * Liste de function API afin de get / ajouter / deleter / modifier un user dans la base de données
    *
    *****************************************************************************************************/

    // Get un les info d'un user dans la base de donnée
    #[Route('/api/getUser/{id}', name:'api_userId', methods:'GET')]
    public function getUserWithId(EntityManagerInterface $em, $id, Request $request): Response
    {
        $userController = new UserController;
        $user = $userController->getUserWithId($em, $id);
        return new Response($user);
    }

    // Get only tuteur from filter 
    #[Route('/api/getTutors/{filter}', name:'api_tutorsFilter', methods:'GET')]
    public function getTutorWithFilter($filter, Request $request): Response
    {
        $userController = new UserController;
        $tutors = $userController->getTutorWithFilter($filter);
        return new Response($tutors);
    }

    // Ajoute un utilisateur dans la base données via l'API
    #[Route('/api/addUser', name:'api_addUser', methods:'POST')]
    public function addUser(EntityManagerInterface $em, Request $request) : Response
    {
        //$userController = new UserController;
        //$reponse = $userController->addUser($em, $request);
        
        $statusController = new StatusController;
        $reponse = $statusController->addUser($em, $request);

        return new Response($reponse);
    }

    // Delete un utilisateur dans la base de données via l'API
    #[Route('/api/deleteUser/{id}', name:'api_deleteUser', methods:'DELETE')]
    public function deleteUser(ManagerRegistry $doctrine, Request $request) : Response
    {
        $userController = new UserController;
        // $response = $userController->deleteUser($doctrine, $id, true);
        return new Response($response);
    }





    /***************************************************************************************************
    *
    * PLAINTE
    * Liste de function API afin de get / ajouter / deleter / modifier un user dans la base de données
    *
    *****************************************************************************************************/

    // Get la liste des plaintes 
    #[Route('/api/complaintList', name:'api_complaintList', methods:'GET')]
    public function getComplaintList(ManagerRegistry $doctrine, Request $request):Response
    {
        $complaintController = new ComplaintController;
        // $complaintList = $complaintController->complaints($doctrine, true);
        return new Response($complaintList);
    }

    // Get un complaint en particulier
    #[Route('/api/complaintFilter', name:'api_getComplaintWithFilter', methods:'GET')]
    public function getComplaint(ManagerRegistry $doctrine, Request $request, $filter): Response
    {
        $complaintController = new ComplaintController;
        // $complaint = $complaintController->getComplaint($doctrine, $filter, true);
        return new Response($complaint);
    }


    // Get une plainte dans la base de données via l'API
    #[Route('/api/getComplaint/{id}', name:'api_getComplaint', methods:'GET')]
    public function getComplaintWithId(ManagerRegistry $doctrine, Request $request) : Response
    {
        $complaintController = new ComplaintController;
        $response = $complaintController->getComplaintWithId($doctrine, $id);
        return new Response($response);
    }



    /***************************************************************************************************
    *
    * MEETING
    * Liste de function API afin de get / ajouter / deleter / modifier un meeting dans la base de données
    *
    *****************************************************************************************************/


    // Get toute les meeting selon le Id de l'utilisateur
    #[Route('/api/myMeetingList/{id}', name:'api_myMeetingList', methods:'GET')]
    public function myMeetingList(ManagerRegistry $doctrine, Request $request, $id) : Response
    {
        $meetingManager = new MeetingController;
        $meetingList = $meetingManager->myMeeting($doctrine, $id);
        return new Response($meetingList);
    }


    // Get toute les info sur un meetings
    #[Route('/api/meetingId/{id}', name:'api_MeetingId', methods:'GET')]
    public function meetingWithId(ManagerRegistry $doctrine, Request $request, $id) : Response
    {
        $meetingManager = new MeetingController;
        $meetingList = $meetingManager->meetingId($doctrine, $id, true);
        return new Response($meetingList);
    }

    // Delete un status dans la base de données
    #[Route('/api/deleteMeeting/{id}', name:'api_deleteMeeting', methods:'DELETE')]
    public function deleteMeeting(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $meetingController = new MeetingController;
        $response = $meetingController->deleteMeeting($doctrine, $id);
        return new Response($response);
    }

    // Route pour ajouter une entrée dans la table Meeting
    #[Route('/api/addMeeting', name:'api_addMeeting', methods:'POST')]
    public function addMeeting(Request $request, EntityManagerInterface $em): Response
    {
        $meetingController = new MeetingController;
        $response = $meetingController->addMeeting($request, $em); 
        return new Response($response);
    }








    /***************************************************************************************************
    *
    * STATUS
    * Liste de function API afin de get / ajouter / deleter / modifier un status dans la base de données
    *
    *****************************************************************************************************/

    //Route aller chercher la liste des Status
    #[Route('/api/statusList', name: 'api_statusList', methods:'GET')]
    public function getListStatus(ManagerRegistry $doctrine, Request $request): Response
    {
            $statusController = new StatusController;
            $listStatus = $statusController->listStatus($doctrine, true);       
            //return json_decode($listStatus, true);
            return new Response($listStatus);
    }

    //Route aller chercher un status selon le ID
    #[Route('/api/status/{id}', name: 'api_statusId', methods:'GET')]
    public function getStatusWithId(ManagerRegistry $doctrine, $id, Request $request): Response
    {   
            $statusController = new StatusController;
            $status = $statusController->status($doctrine, $id, true);       
            return new Response($status);
    }

    // Route pour ajouter une entrée dans la table Status
    #[Route('/api/addStatus', name:'api_addStatus', methods:'POST')]
    public function addStatus(Request $request, EntityManagerInterface $em): Response
    {
        $statusController = new StatusController;
        $response = $statusController->addStatus($request, $em); 
        return new Response($response);
    }

    // Delete un status dans la base de données
    #[Route('/api/deleteStatus/{id}', name:'api_deleteStatus', methods:'DELETE')]
    public function deleteStatus(ManagerRegistry $doctrine,$id, Request $request): Response
    {
        $statusController = new StatusController;
        $response = $statusController->deleteStatus($doctrine, $id, true);
        return new Response($response);
    }

    // Get only tuteur 
    #[Route('/api/getStatus/{filter}', name:'getStatus', methods:'GET')]
    public function getStatusWithFilter(EntityManagerInterface $em, $filter, Request $request): Response
    {
        $statusController = new StatusController;
        $status = $statusController->getStatusWithFilter($em, $filter);
        return new Response($status);
    }

}
