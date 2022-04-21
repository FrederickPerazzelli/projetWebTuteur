<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\StatusRepository;
use App\Entity\Status;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
            $json = $serializer->serialize($listStatus, 'json');
            $response = new Response($json);
            return $response;
        }      
    } 


    // Montre seulement 1 status
    #[Route('/status/{id}', name: 'app_statusId')]
    public function status(ManagerRegistry $doctrine, $id, $API = false): Response
    {
        $status = $this->statusManager($doctrine)->findOneBy(['id' => $id]);
    
        if(!$API){
            return $this->render('status/status.html.twig', [
                'controller_name' => 'StatusController',
                'status' => $status
            ]);
        }else{
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
            $json = $serializer->serialize($status, 'json');
            $response = new Response($json);
            return $response;
        }      
    } 
}
