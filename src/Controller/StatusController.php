<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\StatusRepository;
use App\Entity\Status;

class StatusController extends AbstractController
{

    // ManagerRegistry pour aller chercher dans la database
    private function statusManager(ManagerRegistry $doctrine): StatusRepository
    {
        return $doctrine->getManager()->getRepository(Status::class);
    }


    #[Route('/status', name: 'app_status')]
    public function index(): Response
    {
        return $this->render('status/index.html.twig', [
            'controller_name' => 'StatusController',
        ]);
    }

    // Montre la liste des tous les status
    #[Route('/statusList', name: 'app_statusList')]
    public function listStatus(ManagerRegistry $doctrine, $API = false): Response
    {
        $listStatus = $this->statusManager($doctrine)->findAll();
    
        if(!$API){
            return $this->render('status/listStatus.html.twig', [
                'controller_name' => 'StatusController',
                'status' => $listStatus
            ]);
        }else{
            return json_encode($listStatus, JSON_PRETTY_PRINT);
        }      
    } 
}
