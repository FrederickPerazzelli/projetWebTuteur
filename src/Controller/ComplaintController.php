<?php

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
    public function __construct(private ManagerRegistry $doctrine) {}

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
    #[Route('/changestatus/{statusId}/{complaintId}', name: 'changeStatus')] 
    public function changeStatus(ManagerRegistry $doctrine, $statusId, $complaintId)
    {
        $em = $doctrine->getManager();
        $Produitsrepository = $em->getRepository(Complaint::class);
 
        $produit = $Produitsrepository->find($complaintId);
        $produit->setStatus($statusId);
        $em->persist($produit);
        $em->flush();
    }
}
