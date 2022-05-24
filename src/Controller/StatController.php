<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class StatController extends AbstractController
{
    #[Route('/stat', name: 'app_stat')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $titles = array("Demandes de rencontre", "Rencontres", "Plaintes");
        $subtitles = array("Dernier mois", "Dernières semaine", "Total");

        $demandController = new DemandController;
        $meetingController = new MeetingController;
        $complaintController = new ComplaintController;

        $lastWeekDate = date('Y-m-d', strtotime('-7 days'));
        $lastMonthDate = date('Y-m-d', strtotime('-30 days'));

        
        $complaintTotal = count($complaintController->getComplaints($doctrine));
        $complaintLastWeek = count($complaintController->getComplaintsByDate($doctrine, $lastWeekDate));
        $complaintLastMonth = count($complaintController->getComplaintsByDate($doctrine, $lastMonthDate));

        $meetingTotal = count($meetingController->getMeetings($doctrine));
        $meetingLastWeek = count($meetingController->getMeetingsByDate($doctrine, $lastWeekDate));
        $meetingLastMonth = count($meetingController->getMeetingsByDate($doctrine, $lastMonthDate));

        $requestTotal = count($demandController->getAllDemands($doctrine));
        $requestLastWeek = count($demandController->getDemandByDate($doctrine, $lastWeekDate));
        $requestLastMonth = count($demandController->getDemandByDate($doctrine, $lastMonthDate));

        $requestStats = array( $requestLastMonth, $requestLastWeek, $requestTotal);
        $meetingStats = array( $meetingLastMonth, $meetingLastWeek, $meetingTotal );
        $complaintStats = array( $complaintLastMonth, $complaintLastWeek, $complaintTotal );

        $data = array($requestStats,$meetingStats,$complaintStats);
        

        return $this->render('stat/index.html.twig', [
            'controller_name' => 'StatController',
            'titles' => $titles,
            'subTitles'=> $subtitles,
            'data'=>$data

        ]);
    }
}
