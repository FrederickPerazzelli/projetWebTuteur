<?php
/****************************************
 Fichier : ComplaintController.php
 Auteur : Manuel Turcotte
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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Status;
use App\Entity\Complaint;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ComplaintController extends AbstractController
{
    // ManagerRegistry pour aller chercher dans la database
    private function complaintManager(ManagerRegistry $doctrine): ComplaintRepository
    {
        return $doctrine->getManager()->getRepository(Complaint::class);
    }
    
    // Liste de tout les plaintes
    /**
    * @security("is_granted('ROLE_ADMIN')")
    */
    #[Route('/complaints', name: 'app_complaint')] 
    public function index( ManagerRegistry $doctrine): Response
    {
        $complaints = $this->complaintManager($doctrine)->getAllComplaint();
        $status = $doctrine->getRepository(Status::class)->findBy(['statusType' => 1]);

        return $this->render('complaint/index.html.twig', [
            'controller_name' => 'ComplaintController',
            'complaints'=> $complaints,
            'status' => $status
        ]);
    }

    // Affiche seulement une plainte
    /**
    * @security("is_granted('ROLE_ADMIN')")
    */
    #[Route('/complaint/{id}', name: 'complaint')]
    public function getComplaintId(EntityManagerInterface $doctrine, $id): Response
    {
        $complaint = $doctrine->getRepository(Complaint::class)->find($id);
        $status = $doctrine->getRepository(Status::class)->findBy(['statusType' => 1]);

        return $this->render('complaint/complaint.html.twig', [
            'controller_name' => 'ComplaintController',
            'complaint' => $complaint,
            'status' => $status
        ]);
    }

    #[Route('/complaintchangestatus/{statusId}/{complaintId}', name: 'changeStatus')]  
    public function changeStatus(EntityManagerInterface $doctrine, $statusId, $complaintId): Response
    {
        $complaintsRepository = $doctrine->getRepository(Complaint::class);
        $status = $doctrine->getRepository(Status::class)->findOneBy(['id' => $statusId]);
 
        $complaint = $complaintsRepository->find($complaintId);
        $complaint->setStatus($status);
        $doctrine->persist($complaint);
        $doctrine->flush();

        $response = new Response;
        $response->setStatusCode(200);
        return $response;
    }

    public function assignAdmin()
    {

    }

    #[Route('/delete/{id}', name: 'deleteComplaint')] 
    public function deleteComplaint(ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $Produitsrepository = $em->getRepository(Complaint::class);

        $produit = $Produitsrepository->find($id);
        $em->remove($produit);
        $em->flush();
    }

    // Get Complaint with {id}
    // Renvoie un complaint avec l'id selon le sujet d'étude
	public function getComplaintWithId(ManagerRegistry $doctrine, $id): Response
	{  
		$complaint = $this->complaintManager($doctrine)->getComplaintWithId($id);

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
		$complaint = $this->complaintManager($doctrine)->getAllComplaint();

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
