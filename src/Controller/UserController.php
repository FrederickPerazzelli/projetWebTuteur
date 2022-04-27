<?php
/****************************************
 Fichier : UserController.php
 Auteur : Jean-Nyckolas Roy
 Fonctionnalité : A2, A3
 Date : 2022-04-21
 Vérification :
 Date Nom Approuvé
 =========================================================
 Historique de modifications :
 2022-04-21 - Jean-Nyckolas - Ajout de la route pour la liste d'utilisateurs
 2022-04-26 - Jean-Nyckolas - Ajout des fonction de suppression, activation et désactivation
 2022-04-27 - Frédérick Perazzelli-Delorme - Ajout deux fonctions [ getTutorsWithFilter() et getUserWithId() ]
 =========================================================
****************************************/

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


class UserController extends AbstractController
{
    // Imprime la liste des Users
    #[Route('/users', name: 'users')]
    public function listUsers(EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findAll();
        $roles = $em->getRepository(Role::class)->findAll();

        foreach ($users as $u) {
            if ($u->getImage())
                $u->setImage(base64_encode(stream_get_contents($u->getImage())));
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'userList' => $users,
            'roles' => $roles
        ]);
    }

    // Supprime un user
    #[Route('/deleteUser/{id}', name: 'deleteUser')]
    public function deleteUser(Request $request, EntityManagerInterface $em, $id)
    {   
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        $userRepository->remove($user);
        $em->flush();

        $session = $request->getsession();
        $session->getFlashBag()->add('message', 'L\'utilisateur #' . $id . ' a bien été supprimé');     
    }


     // Supprime un user
     public function deleteUserAPI(Request $request, EntityManagerInterface $em, $id)
     {    
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
 
        if(empty($user)){
 
            $response = new jsonResponse();
            $response->setContent(json_encode('Erreur, l\'utilisateur n\'existe pas'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
 
            return $response;
        }
 
        $userRepository->remove($user);
        $em->flush();
 
        $response = new jsonResponse();
        $response->setContent(json_encode('L\'utilisateur a ete surpprimer'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');
 
        return $response;        
    }


    // Active un user
    #[Route('/activateUser/{id}', name: 'activateUser')]
    public function activateUser(Request $request, EntityManagerInterface $em, $id)
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        $user->setValidAccount(true);
        $em->persist($user);
        $em->flush();

        $session = $request->getsession();
        $session->getFlashBag()->add('message', 'L\'utilisateur #' . $id . ' a bien été activé');
    }

    // Desactive un User
    #[Route('/deactivateUser/{id}', name: 'deactivateUser')]
    public function deactivateUser(Request $request, EntityManagerInterface $em, $id)
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        $user->setValidAccount(false);
        $em->persist($user);
        $em->flush();

        $session = $request->getsession();
        $session->getFlashBag()->add('message', 'L\'utilisateur #' . $id . ' a bien été activé');
    }

    
    // Get User with {filter}
    // Renvoie la liste des tuteurs selon le sujet d'étude
	public function getTutorsWithFilter(EntityManagerInterface $em, $filter): Response
	{
		$listTutors = $em->getRepository(User::class)->findBy(array('role' => 3,  'masteredSubject' => $filter));   
        
		if(empty($listTutors)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur aucun tuteur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;		
		}
		
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
		$json = $serializer->serialize($listTutors, 'json');
		$response = new Response($json);
			
		return $response;
       
    }
	
	// Get User with {id}
    // Renvoie le profil de quelqu'un selon l'id du user
	public function getUserWithId(EntityManagerInterface $em, $id): Response
	{
		
		$user = $em->getRepository(User::class)->findOneBy(['id' => $id]);  

		if(empty($user)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur aucun utilisateur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;		
		}	
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($user, 'json');
        $response = new Response($json);
			
		return $response;
    }
}
