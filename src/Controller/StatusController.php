<?php
/****************************************
 Fichier : ApiController.php
 Auteur : Frederick Perazzelli-Delorme
 Fonctionnalité : *TEST*
 Date : 2022/04/21
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 =========================================================
 À FAIRE :
****************************************/
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\StatusRepository;
use App\Entity\Status;
use App\Entity\StatusType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class StatusController extends AbstractController
{

    // ManagerRegistry pour aller chercher dans la database
    private function statusManager(ManagerRegistry $doctrine): StatusRepository
    {
        return $doctrine->getManager()->getRepository(Status::class);
    }

    // Montre la liste des tous les status
    #[Route('/listStatus', name: 'app_listStatus')]
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

    // Ajouter un status
    public function addStatus(Request $request, EntityManagerInterface $em): Response
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
            $newStatus = new Status;
            $statusType = new StatusType;
            $statusType = $em->getRepository(StatusType::class)->find($body['statusType']);

            $newStatus->setStatusType($statusType);
            $newStatus->setName($body['name']);
            $newStatus->setDescription($body['description']);

            $em->persist($newStatus);
            $em->flush();

            $response = new jsonResponse();
            $response->setContent(json_encode('Le status a été ajouter'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;
    }

    // Supprimer un status
    #[Route('/deleteStatus/{id}', name: 'app_deleteStatus')]
    public function deleteStatus(ManagerRegistry $doctrine, $id, $API = false): Response
    {
        $status = $this->statusManager($doctrine)->findOneBy(['id' => $id]);

        if(!$API){
            $this->statusManager($doctrine)->remove($status); 
            $listeStatus = $this->statusManager($doctrine)->findAll();
            $this->addFlash('add', 'Produit' .$id. 'supprimé');

            return $this->render('produit/listProduits.html.twig', [
                'controller_name' => 'ProduitController',
                'produits' => $listeStatus
            ]);

        }else{
            $this->statusManager($doctrine)->remove($status);

            $response = new jsonResponse();
            $response->setContent(json_encode('Le status a été supprimer'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
            
            return $response;
        }

    }


    // Get Status with filter
    // Renvoie la liste des tuteurs selon le sujet d'étude
	//#[Route('/getTutors/{filter}', name:'app_tutorsFilter')]
	public function getStatusWithFilter(EntityManagerInterface $em, $filter): Response
	{
		$listStatus = $em->getRepository(User::class)->findBy(array('role' => 3,  'masteredSubject' => $filter));

        if(empty($listStatus)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;
		
		}
		
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
		$json = $serializer->serialize($listStatus, 'json');
		$response = new Response($json);
			
		return $response;
        
    }
}
