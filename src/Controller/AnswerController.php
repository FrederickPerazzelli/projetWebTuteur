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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\AnswerRepository;
use Symfony\Component\HttpFoundation\Request;



class AnswerController extends AbstractController
{

    // ManagerRegistry pour aller chercher dans la database
    private function answerManager(ManagerRegistry $doctrine): AnswerRepository
    {
        return $doctrine->getManager()->getRepository(Answer::class);
    }


    // Permet de rajouter une réponse
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

    // Get tout les réponse a une demande
    public function listAnswerDemand(ManagerRegistry $doctrine, $id): Response
    {
        $listAnswer = $this->answerManager($doctrine)->getAnswerFromDemand($id);
    
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($listAnswer, 'json');
        $response = new Response($json);
        return $response;       
    } 

    // Get tout les réponse d'un user
    public function listAnswerUser(ManagerRegistry $doctrine, $id): Response
    {
        $listAnswer = $this->answerManager($doctrine)->getAnswerFromUser($id);
    
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($listAnswer, 'json');
        $response = new Response($json);
        return $response;       
    } 

    // Post rajoute une reponse dans la base de donnée
    public function addAnswer(Request $request, EntityManagerInterface $em): Response
    {
        $body = json_decode(
            $request->getContent(), true
        );

        if(empty($body)){

            $json = new jsonResponse($body);
            $json->setContent(json_encode('Erreur'));
            $json->headers->set('Content-Type', 'application/json');
            $json->setCharset('UTF-8');

            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
            $json = $serializer->serialize($user, 'json');
            $response = new Response($json);
            
            return $response;
        }

        /* 
        $newAnswerMobile = unserialize($body['answer'])
        $newAnswer = new Answer($newAnswer);

        $em->persist($newAnswer);
        $em->flush();
        */

        $newAnswer = new Answer;

        $newDemand = new Demand;
        $newDemand = $em->getRepository(Demand::class)->find($body['demand']);

        $newUser = new User;
        $newUser = $em->getRepository(User::class)->find($body['user']);

        $newDate = new \dateTime($body['dateTime']);

        $newAnswer->setDemand($newDemand);
        $newAnswer->setUser($newUser);
        $newAnswer->setAnswerDate($newDate);
        $newAnswer->setComments($body['comment']);

        $em->persist($newAnswer);
        $em->flush();
        
        $json = new jsonResponse($body);
        $json->setContent(json_encode('La reponse a ete ajouter'));
        $json->headers->set('Content-Type', 'application/json');
        $json->setCharset('UTF-8');

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($newAnswer, 'json');
        $response = new Response($json);
        
        return $response;

    }

     // Delete une reponse
    public function deleteAnswer(ManagerRegistry $doctrine, $id):Response
    {   
        $answer = $this->answerManager($doctrine)->findOneBy(['id' => $id]);
         
        if(empty($answer)){
 
            $this->answerManager($doctrine)->remove($answer);
             
            $response = new jsonResponse();
            $response->setContent(json_encode('impossible de supprimer'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
                 
            return $response;
 
        }
         
        $this->answerManager($doctrine)->remove($answer);
 
        $response = new jsonResponse();
        $response->setContent(json_encode('La reponse a ete supprimer'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');
                 
        return $response;
    }
}
