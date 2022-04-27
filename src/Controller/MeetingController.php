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

class MeetingController extends AbstractController
{
    // ManagerRegistry pour aller chercher dans la database
    private function meetingManager(ManagerRegistry $doctrine): MeetingRepository
    {
        return $doctrine->getManager()->getRepository(Meeting::class);
    }

    // Liste de tout les meetings
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
    #[Route('/meeting/{id}', name:'app_meetingId')]
    public function meetingId(ManagerRegistry $doctrine, $id, $API = false):Response
    {   
        $meeting = $this->meetingManager($doctrine)->find($id);

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
}
