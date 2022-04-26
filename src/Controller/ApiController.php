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


class ApiController extends AbstractController
{
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
            //return json_decode($listStatus, true);
            return new Response($status);
    }

    // Delete un status dans la base de données
    #[Route('/api/deleteStatus/{id}', name:'api_deleteStatus', methods:'DELETE')]
    public function deleteStatus(ManagerRegistry $doctrine,$id, Request $request): Response
    {
        $statusController = new StatusController;
        $response = $statusController->deleteStatus($doctrine, $id, true);
        return new Response($response);
    }

    /*****************
    * UTILISATEUR
    * Liste de function API afin de get / ajouter / deleter / modifier un user dans la base de données
    ****************/

    // Get la liste de touts les users
    #[Route('/api/userList', name:'api_userList', methods:('GET'))]
    public function getUserList(ManagerRegistry $doctrine, Request $request): Response
    {
        $userController = new StatusController;
        //$listUser = $userController->userList($doctrine, true);       
        //return json_decode($listStatus, true);
        return new Response($listUser);
    }

    // Get un les info d'un user dans la base de donnée
    #[Route('/api/getUser/{id}', name:'api_userId', methods:'GET')]
    public function getUserWithId(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $userController = new UserController;
        //$user = $userController->getUserWithId($doctrine, $id, True);
        return new Response($user);
    }

    // Ajoute un utilisateur dans la base données via l'API
    #[Route('/api/addUser', name:'api_addUser', methods:'POST')]
    public function addUser(ManagerRegistry $doctrine, Request $request) : Response
    {
        $userController = new UserController;
        // $reponse = $userController->addUser($doctrine, jsonArray[], true);
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

    /*****************
    * PLAINTE
    * Liste de function API afin de get / ajouter / deleter / modifier un user dans la base de données
    ****************/

    // Get la liste des plaintes 
    #[Route('/api/complaintList', name:'api_complaintList', methods:'GET')]
    public function getComplaintList(ManagerRegistry $doctrine, Request $request):Response
    {
        $complaintController = new ComplaintController;
        // $complaintList = $complaintController->complaintList($doctrine, true);
        return new Response($complaintList);
    }

    // Get un complaint en particulier
    #[Route('/api/complaint/{id}', name:'api_complaintId', methods:'GET')]
    public function getComplaintWithId(ManagerRegistry $doctrine, Request $request): Response
    {
        $complaintController = new ComplaintController;
        // $complaint = $complaintController->getComplaintWithId($doctrine, true);
        return new Response($complaint);
    }

    // Get complaint from User
    #[Route('/api/complaint/{user}', name:'api_complaintUser', methods:'GET')]
    public function getComplaintFromUser(ManagerRegistry $doctrine, $user, Request $request): Response
    {
        $complainteController = new ComplaintController;
        // $complaints = $complaintController->getComplaintFromUser($doctrine, $user, true);
        return new Response($complaints);
    }

    // Get complaint from status
    #[Route('/api/complaint/{status}', name:'api_complaintStatus', methods:'GET')]
    public function getComplaintFromStatus(ManagerRegistry $doctrine, $status, Request $request): Response
    {
        $complaintController = new ComplaintController;
        // $complaints = $complaintController->getComplaintFromStatus($doctrine, $status, true);
        return new Response($complaints);
    }

    // Get complaint from admin
    #[Route('/api/complaint/{admin}', name:'api_complaintAdmin', methods:'GET')]
    public function getComplaintFromAdmin(ManagerRegistry $doctrine, $admin, Request $request): Response
    {
        $complaintController = new ComplaintController;
        // $complaints = $complaintController->getComplaintFromAdmin($doctrine, $status, true);
        return new Response($complaints);
    }

    


}
