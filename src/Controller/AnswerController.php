<?php
/****************************************
 Fichier : AnswerController.php
 Auteur : Jean-Nyckolas Roy
 Fonctionnalité : A12
 Date : 2022-04-28
 =========================================================
 Historique de modifications :
 2022-04-28 - Jean-Nyckolas - Ajout de la requête d'ajout d'un commentaire
 =========================================================
****************************************/

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
        $userObject = $em->getRepository(User::class)->find($user);
        $answer = new Answer();
        $answer->setDemand($demandObject);
        $answer->setUser($userObject);
        $answer->setComments($comments);
        $answer->setAnswerDate(new \DateTime());

        $em->persist($answer);
        $em->flush();

        return $this->redirect($this->generateUrl('/demand/'+$demand));
    }
}
