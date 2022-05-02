<?php
/****************************************
 Fichier : ApiController.php
 Auteur : Jean-Nyckolas
 Fonctionnalité : A12
 Date : 2022/04/28
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 =========================================================
 À FAIRE :
****************************************/
namespace App\Controller;

use App\Entity\Demand;
use App\Entity\Answer;
use App\Entity\Status;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\DemandRepository;

class DemandController extends AbstractController
{
    // ManagerRegistry pour aller chercher dans la database
    private function demandManager(ManagerRegistry $doctrine): DemandRepository
    {
        return $doctrine->getManager()->getRepository(Demand::class);
    }

    // Affiche la liste de toute les demandes
    #[Route('/demands', name: 'demands')]
    public function getDemands(EntityManagerInterface $em): Response
    {
        $demands = $em->getRepository(Demand::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();
        $status = $em->getRepository(Status::class)->findBy(['statusType' => 3]);

        return $this->render('demand/index.html.twig', [
            'controller_name' => 'DemandController',
            'demands' => $demands,
            'status' => $status,
            'categories' => $categories
        ]);
    }

    // Affiche seulement une demande
    #[Route('/demand/{id}', name: 'demand')]
    public function getDemand(EntityManagerInterface $em, $id): Response
    {
        $demand = $em->getRepository(Demand::class)->find($id);
        $sameSubject = $em->getRepository(Demand::class)->findBy(['category' => $demand->getCategory()]);
        $answers = $em->getRepository(Answer::class)->findBy(['demand' => $id]);

        return $this->render('demand/demand.html.twig', [
            'controller_name' => 'DemandController',
            'demand' => $demand,
            'sameSubject' => $sameSubject,
            'answers' => $answers
        ]);
    }

    // Get toute les demande 
    public function listDemand(ManagerRegistry $doctrine): Response
    {
        $listDemand = $this->demandManager($doctrine)->getAllDemand();
    
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($listDemand, 'json');
        $response = new Response($json);
        return $response;       
    } 


    // Get toute les demandes selon le id de l'utilisateur
    public function getDemandWithId(ManagerRegistry $doctrine, $id): Response
    {
        $demand = $this->demandManager($doctrine)->getInfoDemand($id);
      
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($demand, 'json');
        $response = new Response($json);
        return $response;
    
    } 

    
}
