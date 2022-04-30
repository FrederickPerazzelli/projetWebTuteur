<?php
/****************************************
 Fichier : ComplaintController.php
 Auteur : William Goupil
 Fonctionnalité : A2, A3
 Date : 2022-04-21
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 2022-04-27 - Frédérick Perazzelli-Delorme - Ajout deux fonctions [ getComplaintWithId() ]
 =========================================================
****************************************/
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Complaint;
use App\Entity\User;
use App\Entity\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ComplaintController extends AbstractController
{
    
    //@security("is_granted('ROLE_ADMIN')")
    
    #[Route('/complaint', name: 'app_complaint')] 
    public function index( ManagerRegistry $doctrine): Response
    {
        $statusManager = $doctrine->getManager()->getRepository(Status::class);

        $complaintList = $this->getComplaints($doctrine);
        $statusList = $statusManager->findAll();

        return $this->render('complaint/index.html.twig', [
            'controller_name' => 'ComplaintController',
            'complaints'=> $complaintList,
            'statusList' => $statusList
        ]);
    }
    public function getComplaint(ManagerRegistry $doctrine, array $filter)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->find($filter);
    }
    public function getComplaints(ManagerRegistry $doctrine)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->findAll();
    }
    #[Route('/delete/{id}', name: 'changeStatus')] 

    public function deleteComplaint(ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $Produitsrepository = $em->getRepository(Complaint::class);

        $produit = $Produitsrepository->find($id);
        $em->remove($produit);
        $em->flush();
    }
    public function assignAdmin()
    {

    }
    #[Route('/complaintchangestatus/{statusId}/{complaintId}', name: 'changeStatus')] 
    public function changeStatus(ManagerRegistry $doctrine, $statusId, $complaintId)
    {
        $em = $doctrine->getManager();
        $Produitsrepository = $em->getRepository(Complaint::class);
 
        $produit = $Produitsrepository->find($complaintId);
        $produit->setStatus($statusId);
        $em->persist($produit);
        $em->flush();
    }

    // Get Complaint with {id}
    // Renvoie un complaint avec l'id selon le sujet d'étude
	public function getComplaintWithId(ManagerRegistry $doctrine, $id): Response
	{  
		$complaintManager = $doctrine->getManager()->getRepository(Complaint::class);
		$complaint = $complaintManager->getComplaintWithId($id);

		if(empty($complaint)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;
		
		}
		
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($complaint, 'json');
        $response = new Response($json);
			
		return $response;
    }

    // Get all complaint 
    // Renvoie la liste de toutes les plaintes
	public function getAllComplaints(ManagerRegistry $doctrine): Response
	{  
		$complaintManager = $doctrine->getManager()->getRepository(Complaint::class);
		$complaint = $complaintManager->getAllComplaint();

		if(empty($complaint)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;
		
		}
		
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($complaint, 'json');
        $response = new Response($json);
			
		return $response;
    }
}
