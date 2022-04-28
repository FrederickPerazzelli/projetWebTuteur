<?php

namespace App\Controller;

use App\Entity\Demand;
use App\Entity\Answer;
use App\Entity\Status;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DemandController extends AbstractController
{
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
}
