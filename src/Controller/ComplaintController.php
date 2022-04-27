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


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Complaint;
use App\Entity\User;
use App\Entity\Status;

class ComplaintController extends AbstractController
{
    #[Route('/complaint', name: 'app_complaint')] 
    public function index(ManagerRegistry $doctrine): Response
    {
        $complaintManager = $doctrine->getManager()->getRepository(Complaint::class);
        $userManager = $doctrine->getManager()->getRepository(User::class);
        $statusManager = $doctrine->getManager()->getRepository(Status::class);

        $complaintList = $complaintManager->findAll();
        $displayedInfo = [];
        foreach($complaintList as $complaint)
        {
            $currentUser = $complaint->getUser();   
            $currentStatus = $complaint->getStatus();
            
            $formatedInfo = [
                "userId" => $currentUser->getId(), 
                "userName" => $currentUser->getFirstName() . " " . $currentUser->getLastName(),
                "ComplaintId" => $complaint->getId(),
                "description" => $complaint->getDescription(),
                "date" => $complaint->getComplaintDate(),
                "statusId" => $currentStatus->getId(),
                "adminId" => $complaint->getAdmin()->getId()
            ];
            array_push($displayedInfo , $formatedInfo);
        }
        $statusList = $statusManager->find(array('id' => '1'));

        return $this->render('complaint/index.html.twig', [
            'controller_name' => 'ComplaintController',
            'complaints'=> $displayedInfo,
            'statusList' => $statusList
        ]);
    }
    public function getComplaint($filter)
    {

    }

    // Get Complaint with {id}
    // Renvoie un complaint avec l'id selon le sujet d'étude
	public function getComplaintWithId(ManagerRegistry $doctrine, $id): Response
	{  
		$complaintManager = $doctrine->getManager()->getRepository(Complaint::class);
		$complaint = $ComplaintManager($doctrine)->findOneBy(['id' => $id]);

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
