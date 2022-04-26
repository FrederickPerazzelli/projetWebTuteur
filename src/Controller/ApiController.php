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

}
