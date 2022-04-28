<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\User;
use App\Entity\Demand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AnswerController extends AbstractController
{
    #[Route('/addComment/{demand}/{user}/{comments}', name: 'addComment')]
    public function addComment(EntityManagerInterface $em, $demand, $user, $comments)
    {
        $demandObject = $em->getRepository(Demand::class)->find($demand);
        $userObject = $em->getRepository(Demand::class)->find($user);
        $answer = new Answer();
        $answer->setDemand($demandObject);
        $answer->setUser($userObject);
        $answer->setComments($comments);

        $em->persist($answer);
        $em->flush();
    }
}
