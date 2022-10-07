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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Complaint;
use App\Entity\User;
use App\Entity\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ComplaintController extends AbstractController
{
    //display the complaints view
    #[Route('/complaint', name: 'app_complaint')] 
    public function index( ManagerRegistry $doctrine): Response
    {
        $statusManager = $doctrine->getManager()->getRepository(Status::class);
        $userManager = $doctrine->getManager()->getRepository(User::class);

        $newComplaintList = $this->getComplaintsBy($doctrine,array('status'=>1));
        $openComplaintList = $this->getComplaintsBy($doctrine,array('status'=>2));
        $closeComplaintList = $this->getComplaintsBy($doctrine,array('status'=>3));


        $statusList = $statusManager->findAll();

        return $this->render('complaint/index.html.twig', [
            'controller_name' => 'ComplaintController',
            'newComplaints'=> $newComplaintList,
            'openComplaints'=> $openComplaintList,
            'closeComplaints'=> $closeComplaintList,
            'statusList' => $statusList
        ]);
    }
    //get a complaint with a specific filter
    public function getComplaint(ManagerRegistry $doctrine, array $filter)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->find($filter);
    }
    //get multiple complaints with a specific filter
    public function getComplaintsBy(ManagerRegistry $doctrine, array $filter)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->findBy($filter);
    }
    //get multiple complaints with a specific filter
    public function getComplaintsByDate(ManagerRegistry $doctrine, $date)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->getComplaintWithDate($date);
    }
    //get all complaints
    public function getComplaints(ManagerRegistry $doctrine)
    {
        return $doctrine->getManager()->getRepository(Complaint::class)->findAll();
    }
    //delete a complaint
    #[Route('/delete/{id}', name: 'delete')] 
    public function deleteComplaint(ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $Complaintssrepository = $em->getRepository(Complaint::class);

        $complaint = $Complaintssrepository->find($id);
        $em->remove($complaint);
        $em->flush();
    }
    //delete a complaint from the web view
    #[Route('/delete', name: 'delete')]
    public function deleteComplaintWeb(ManagerRegistry $doctrine, Request $request) : Response
    {

        $body = json_decode(
            $request->getContent(), true
        );
        $complaintId = $body['complaint_Id'];
        $this->deleteComplaint($doctrine, $complaintId);

        $response = new jsonResponse($body);
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');

        return $response;   
    }
    //change the status of a complaint and assign an admin to it
    #[Route('/openComplaint', name: 'openComplaint')] 
    public function openComplaint(ManagerRegistry $doctrine, Request $request) : Response
    {
        $body = json_decode(
            $request->getContent(), true
        );
        $complaintId = $body['complaint_Id'];

        $em = $doctrine->getManager();

        $statusRepository = $em->getRepository(Status::class);
        $Complaintrepository = $em->getRepository(Complaint::class);

        $status = $statusRepository->find(2);
        $user = $this->getUser();
        $complaint = $Complaintrepository->find($complaintId);

        $complaint->setAdmin($user);
        $complaint->setStatus($status);
        $em->persist($complaint);
        $em->flush();

        $response = new jsonResponse($body);
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');

        return $response;    
    }
    //change the status of a complaint
    #[Route('/complaintchangestatus', name: 'changeStatus')] 
    public function changeStatus(ManagerRegistry $doctrine, Request $request) : Response
    {        
        $body = json_decode(
            $request->getContent(), true
        );
        $statusId = $body['status_Id'];
        $complaintId = $body['complaint_Id'];
        $em = $doctrine->getManager();
        
        $statusRepository = $em->getRepository(Status::class);

        $status = $statusRepository->find($statusId);
        
        $Complaintrepository = $em->getRepository(Complaint::class);
        
        $complaint = $Complaintrepository->find($complaintId);
        $complaint->setStatus($status);
        $em->persist($complaint);
        $em->flush();

        $response = new jsonResponse($body);
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');

        return $response;    
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

		if(empty($complaint))
        {
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

    public function addComplaint(EntityManagerInterface $em, Request $request) {
        $body = json_decode(
            $request->getContent(), true
        );

        $userRepository = $em->getRepository(User::class);
        $status = $em->getRepository(Status::class)->find(1);

        $user = $userRepository->find($body["user"]);
        $admin = $userRepository->find(1);

        $complaint = new Complaint();

        $complaint->setUser($user);
        $complaint->setAdmin($admin);
        $complaint->setStatus($status);
        $newDate = new \dateTime('now');
        $complaint->setComplaintDate($newDate);
        $complaint->setDescription($body["description"]);

        $em->persist($complaint);
        $em->flush();

        $response = new jsonResponse($body);
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');

        return $response;
    }
}
