<?php

namespace App\Controller;

use App\Entity\Demand;
use App\Entity\Answer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemandController extends AbstractController
{
    #[Route('/demands', name: 'demands')]
    public function getDemands(): Response
    {
        return $this->render('demand/index.html.twig', [
            'controller_name' => 'DemandController',
        ]);
    }
}
