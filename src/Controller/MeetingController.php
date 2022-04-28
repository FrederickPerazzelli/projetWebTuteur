<?php
/****************************************
 Fichier : MeetingController.php
 Auteur : Frédérick Perazzelli-Delorme
 Fonctionnalité : A5
 Date : 2022-04-27
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 =========================================================
****************************************/
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Status;
use App\Entity\Meeting;
use App\Form\MeetingType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\MeetingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class MeetingController extends AbstractController
{
    // ManagerRegistry pour aller chercher dans la database
    private function meetingManager(ManagerRegistry $doctrine): MeetingRepository
    {
        return $doctrine->getManager()->getRepository(Meeting::class);
    }

    // Liste de tout les meetings
    /**
    * @security("is_granted('ROLE_ADMIN')")
    */
    #[Route('/meeting', name: 'app_meeting')]
    public function meetings(ManagerRegistry $doctrine): Response
    {
        $meetingList = $this->meetingManager($doctrine)->findAll();

        return $this->render('meeting/index.html.twig', [
            'controller_name' => 'MeetingController',
            'meetings' => $meetingList
        ]);
    }

    // Affiche seulement 1 rencontre selon le id
    /**
    * @security("is_granted('ROLE_ADMIN')")
    */
    #[Route('/meeting/{id}', name:'app_meetingId')]
    public function meetingId(ManagerRegistry $doctrine, $id, $API = false):Response
    {   
        $meeting = $this->meetingManager($doctrine)->getInfoMeeting($id);

        if(!$API){
            
            $meeting = $this->meetingManager($doctrine)->find($id);

            $form = $this->createForm(MeetingType::class, $meeting);

            return $this->render('meeting/meeting.html.twig', [
                'controller_name' => 'MeetingController',
                'form' => $form->createView(),
                'meeting' => $meeting
            ]);

        }else{

            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
            $json = $serializer->serialize($meeting, 'json');
            $response = new Response($json);
            return $response;
        }
        
    }

    //Renvoie tout les meeting que la personne possède
    public function myMeeting(ManagerRegistry $doctrine, $id):Response
    {   
        $meetingList = $this->meetingManager($doctrine)->getAllMyMeeting($id);

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($meetingList, 'json');
        $response = new Response($json);
        return $response;    
    }

    // Post rajoute un meeting dans la base de donnée
    //#[Route('/addMeeting', name:'app_addMeeting')]
    public function addMeeting(Request $request, EntityManagerInterface $em): Response
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

        $newMeeting = new Meeting;

        $newStatus = new Status;
        $newStatus = $em->getRepository(Status::class)->find($body['status']);

        $newStudent = new User;
        $newStudent = $em->getRepository(User::class)->find($body['student']);

        $newTutor = new User;
        $newTutor = $em->getRepository(User::class)->find($body['tutor']);

        $newDate = new \dateTime($body['date']);
        //newDate->setDate($body['year'], $body['month'], $body['day']);

        $newTime = new \dateTime($body['time']);
        //$newTime->setTime($body['hour'], $body['minute'], $body['second']);

        $newMeeting->setDate($newDate);
        $newMeeting->setMeetingTime($newTime);
        $newMeeting->setLocation($body['location']);
        $newMeeting->setComments($body['comments']);
        $newMeeting->setMotive($body['motive']);
        $newMeeting->setStatus($newStatus);
        $newMeeting->setStudent($newStudent);
        $newMeeting->setTutor($newTutor);

        $em->persist($newMeeting);
        $em->flush();
        
        
        $response = new jsonResponse($body);
        $response->setContent(json_encode('Le status a été ajouter'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');

        return $response;

    }




    // Delete une rencontre
    public function deleteMeeting(ManagerRegistry $doctrine, $id):Response
    {   
        $meeting = $this->meetingManager($doctrine)->findOneBy(['id' => $id]);
        
        if(empty($meeting)){

            $this->meetingManager($doctrine)->remove($meeting);

            $response = new jsonResponse();
            $response->setContent(json_encode('impossible de supprimer'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
                
            return $response;

        }
        
        $this->meetingManager($doctrine)->remove($meeting);

        $response = new jsonResponse();
        $response->setContent(json_encode('Le status a été supprimer'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');
                
        return $response;
    }
}
