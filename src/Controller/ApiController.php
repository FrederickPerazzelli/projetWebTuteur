<?php

namespace App\Controller;

header('Content-Type: application/json');
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use App\Controller\StatusController;
use Doctrine\Persistence\ManagerRegistry;

class ApiController extends AbstractController
{
    //Route aller chercher la liste des Status
    #[Route('/api/listeStatus', name: 'app_api_listStatus')]
    public function listeStatus(ManagerRegistry $doctrine): Response
    {
        $statusController = new StatusController;
        $listStatus = $statusController->listStatus($doctrine, true);       
        //return json_decode($listStatus, true);
        return new Response($listStatus);
    }

    //Route aller chercher la liste des Status
    #[Route('/api/status/{id}', name: 'app_api_statusId')]
    public function status(ManagerRegistry $doctrine, $id): Response
    {
        $statusController = new StatusController;
        $listStatus = $statusController->status($doctrine, $id, true);       
        //return json_decode($listStatus, true);
        return new Response($listStatus);
    }
}
