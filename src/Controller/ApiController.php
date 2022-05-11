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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        return new Response($user->getContent());
    }

    // Get only tuteur from filter 
    #[Route('/api/getTutors/{filter}', name:'api_tutorsFilter', methods:'GET')]
    public function getTutorWithFilter(EntityManagerInterface $em, $filter, Request $request): Response
    {
        $userController = new UserController;
        $tutors = $userController->getTutorsWithFilter($em, $filter);
        return new Response($tutors);
    }

    // Get all tutors from database
    #[Route('/api/getAllTutors', name:'get_allTutors', methods:'GET')]
    public function getAllTutors(EntityManagerInterface $em, Request $request) : Response
    {
        $userController = new UserController;
        $tutorsList = $userController->getAllTutors($em);
        return new Response($tutorsList);
    }

    // Get compare les email
    #[Route('/api/compareEmail/{email}', name:'get_allTutors', methods:'GET')]
    public function compareEmail(EntityManagerInterface $em, $email, Request $request) : Response
    {
        $userController = new UserController;
        $response = $userController->compareEmail($em, $email);
        return new Response($response);
    }

    // Ajoute un utilisateur dans la base données via l'API
    #[Route('/api/addUser', name:'api_addUser', methods:'POST')]
    public function addUser(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher, Request $request) : Response
    {        
        $userController = new UserController;
        $reponse = $userController->addUser($request, $em, $userPasswordHasher);

        return new Response($reponse->getContent());
    }

    // Delete un utilisateur dans la base de données via l'API
    #[Route('/api/deleteUser/{id}', name:'api_deleteUser', methods:'DELETE')]
    public function deleteUser(EntityManagerInterface $em, $id, Request $request) : Response
    {
        $userController = new UserController;
        $response = $userController->deleteUserAPI($request, $em, $id, true);
        return new Response($response);
    }

    // Authentification 
    #[Route('/api/login', name:'api_login', methods:'POST')]
    public function mobileLogin(EntityManagerInterface $em, Request $request): Response
    {
        $securityController = new SecurityController;
        $response = $securityController->mobileLogin($request, $em);
        return new Response($response->getContent());
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
        $complaintList = $complaintController->getAllComplaints($doctrine);
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
    public function getComplaintWithId(ManagerRegistry $doctrine, $id, Request $request) : Response
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

    // Delete un meeting dans la base de données
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
    * DEMANDE
    * Liste de function API afin de get / ajouter / deleter / modifier une demande dans la base de données
    *
    *****************************************************************************************************/

    //Route aller chercher la liste des demandes
    #[Route('/api/demandList', name: 'api_demandList', methods:'GET')]
    public function getListDemand(ManagerRegistry $doctrine, Request $request): Response
    {
            $demandController = new DemandController;
            $listDemand = $demandController->listDemand($doctrine);       
            return new Response($listDemand);
    }

    //Route aller chercher une demande selon le ID
    #[Route('/api/demand/{id}', name: 'api_demandId', methods:'GET')]
    public function getDemandWIthiD(ManagerRegistry $doctrine, $id, Request $request): Response
    {   
            $demandController = new DemandController;
            $demand = $demandController->getDemandWithId($doctrine, $id);       
            return new Response($demand);
    }

    // Route pour aller ajouter une demande
    #[Route('/api/addDemand', name:'api_addDemand', methods:'POST')]
    public function addDemand(Request $request, EntityManagerInterface $em): Response
    {
        $demandController = new DemandController;
        $response = $demandController->addDemand($request, $em); 
        return new Response($response->getContent());
    }

    // Delete une demande dans la base de données
    #[Route('/api/deleteDemand/{id}', name:'api_deleteDemand', methods:'DELETE')]
    public function deleteDemand(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $demandController = new DemandController;
        $response = $demandController->deleteDemand($doctrine, $id);
        return new Response($response);
    }

    /***************************************************************************************************
    *
    * ANSWER
    * Liste de function API afin de get / ajouter / deleter / modifier une réponse dans la base de données
    *
    *****************************************************************************************************/

    //Route aller chercher la liste des reponse a une demande
    #[Route('/api/answerListDemand/{id}', name: 'api_answerListDemand', methods:'GET')]
    public function getListAnswerDemand(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $answerController = new AnswerController;
        $listAnswerDemand = $answerController->listAnswerDemand($doctrine, $id);       
        return new Response($listAnswerDemand);
    }

    //Route aller chercher la liste des reponse a une demande
    #[Route('/api/answerListUser/{id}', name: 'api_answerListUser', methods:'GET')]
    public function getListAnswerUser(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $answerController = new AnswerController;
        $listAnswerUser = $answerController->listAnswerUser($doctrine, $id);       
        return new Response($listAnswerUser);
    }

    // Route pour aller ajouter une demande
    #[Route('/api/addAnswer', name:'api_addAnswer', methods:'POST')]
    public function addAnswer(Request $request, EntityManagerInterface $em): Response
    {
        $answerController = new AnswerController;
        $response = $answerController->addAnswer($request, $em); 
        return new Response($response->getContent());
    }

    // Delete une reponse dans la base de données
    #[Route('/api/deleteAnswer/{id}', name:'api_deleteAnswer', methods:'DELETE')]
    public function deleteAnswer(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $answerController = new AnswerController;
        $response = $answerController->deleteAnswer($doctrine, $id);
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

    
    /***************************************************************************************************
    *
    * Catégorie
    * Liste de function API afin de get / ajouter / deleter / modifier une catégorie dans la base de données
    *
    *****************************************************************************************************/

    //Route aller chercher la liste des cagories
    #[Route('/api/categoryList', name: 'api_categoryList', methods:'GET')]
    public function getListCategory(ManagerRegistry $doctrine, Request $request): Response
    {
            $categoryController = new CategoryController;
            $listcategory = $categoryController->getListCategory($doctrine, $request);       
            //return json_decode($listStatus, true);
            return new Response($listcategory);
    }
}
