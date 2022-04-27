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
        $answers = $em->getRepository(Answer::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();
        $status = $em->getRepository(Status::class)->findBy(['statusType' => 3]);

        return $this->render('demand/index.html.twig', [
            'controller_name' => 'DemandController',
            'demands' => $demands,
            'answers' => $answers,
            'status' => $status,
            'categories' => $categories
        ]);
    }
}
