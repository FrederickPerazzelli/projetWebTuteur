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
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\Request;

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

    // Post rajoute une demande dans la base de donnée
    //#[Route('/addMeeting', name:'app_addMeeting')]
    public function addDemand(Request $request, EntityManagerInterface $em): Response
    {
        $body = json_decode(
            $request->getContent(), true
        );

        if(empty($body)){

            $response = new jsonResponse();
            $response->setContent(json_encode('Erreur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;
        }

        /* 
        $newDemandMobile = unserialize($body['demands'])
        $newDemand = new Demand($newDemand);

        $em->persist($newDemand);
        $em->flush();
        */

        $newDemand = new Demand;

        $newStatus = new Status;
        $newStatus = $em->getRepository(Status::class)->find($body['status']);

        $newCategory = new User;
        $newCategory = $em->getRepository(Category::class)->find($body['category']);

        $newUser = new User;
        $newUser = $em->getRepository(User::class)->find($body['user']);

        $newDate = new \dateTime($body['dateTime']);

        $newDemand->setTitle($body['title']);
        $newDemand->setSubject($body['subject']);
        $newDemand->setCategory($newCategory);
        $newDemand->setPublishDate($newDate);
        $newDemand->setComments($body['comment']);
        $newDemand->setUser($newUser);
        $newDemand->setStatus($newStatus);

        $em->persist($newDemand);
        $em->flush();
        
        $json = new jsonResponse($body);
        $json->setContent(json_encode('La reponse a ete ajouter'));
        $json->headers->set('Content-Type', 'application/json');
        $json->setCharset('UTF-8');

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($newDemand, 'json');
        $response = new Response($json);
        
        return $response;
    }


    // Delete une demande
    public function deleteDemand(ManagerRegistry $doctrine, $id):Response
    {   
        $demand = $this->demandManager($doctrine)->findOneBy(['id' => $id]);
        
        if(empty($demand)){

            $this->demandManager($doctrine)->remove($demand);

            $response = new jsonResponse();
            $response->setContent(json_encode('impossible de supprimer'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
                
            return $response;

        }
        
        $this->demandManager($doctrine)->remove($demand);

        $response = new jsonResponse();
        $response->setContent(json_encode('La demande a ete supprimer'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');
                
        return $response;
    }
}
