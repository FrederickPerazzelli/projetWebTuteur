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
 2022-04-27 - Frédérick Perazzelli-Delorme - Ajout deux fonctions [ getTutorsWithFilter(), getUserWithId() et deleteUserAPI() ]
 =========================================================
****************************************/

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Category;
use App\Entity\Meeting;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    // Imprime la liste des Users
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
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

    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
    #[Route('/profile/{id}', name: 'profile')]
    public function getProfile(Request $request, EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        $studentMeetings = $em->getRepository(Meeting::class)->findBy(['student' => $id]);
        $tutorMeetings = $em->getRepository(Meeting::class)->findBy(['tutor' => $id]);
        $image = $user->getImage();

        if ($image)
            $image = base64_encode(stream_get_contents($image));

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($user->getPhone())
                $user->setPhone(preg_replace('/[^0-9]/', '', $user->getPhone()));
            $em->persist($user);
            $em->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Le profil #' . $id . ' a bien été modifié');

            return $this->redirect($this->generateUrl('users'));
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'studentMeetings' => $studentMeetings,
            'tutorMeetings' => $tutorMeetings,
            'image' => $image
        ]);
    }

    // Supprime un user
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
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


    // Supprime un user via L'API
    public function deleteUserAPI(Request $request, EntityManagerInterface $em, $id): Response
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
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
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
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
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
        
		$listTutors = $em->getRepository(User::class)->getTutorsWithFilter($filter);   
        
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

    // Get all tutors
    // Renvoie la liste de toutes les tuteurs
	public function getAllTutors(EntityManagerInterface $em): Response
	{
		
		$listTutors = $em->getRepository(User::class)->getAllTutors();  

		if(empty($listTutors)){
		
			$response = new jsonResponse();
            $response->setContent(json_encode('Erreur aucun Tuteur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');

            return $response;		
		}	
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($listTutors, 'json');
        $response = new Response($json);
			
		return $response;
    }

    // Get User
    public function compareEmail(EntityManagerInterface $em, $email): Response
	{
		
		if($emailCompare = $em->getRepository(User::class)->getEmail($email))
        {
            $response = new jsonResponse();
            $response->setContent(json_encode('True'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
 
            return $response;
            
        }else{

            $response = new jsonResponse();
            $response->setContent(json_encode('False'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
 
            return $response;
        }
    }
    
    // Post rajoute un user dans la base de donnée
    public function addUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $body = json_decode(
            $request->getContent(), true
        );
 
        if(empty($body)){
 
            $response = new jsonResponse();
            $response->setContent(json_encode('Erreur'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
 
            return $response;
        }
 
        if($emailCompare = $em->getRepository(User::class)->getEmail($body['email']))
        {
            $response = new jsonResponse();
            $response->setContent(json_encode('Email existe deja'));
            $response->headers->set('Content-Type', 'application/json');
            $response->setCharset('UTF-8');
 
            return $response;
        }

        /* 
         $newUserFromMobile = unserialize($body['user'])
         $newUser = new Answer($newUserFromMobile);
 
         $em->persist($newUser);
         $em->flush();
        */
 
        $newUser = new User;
 
        $newRole = new Role;
        $newRole = $em->getRepository(Role::class)->find($body['role']);

        if (isset($body['masteredSubject']) && $body['masteredSubject'] != null) {
            $newCategory = new Category;
            $newCategory = $em->getRepository(Category::class)->find($body['masteredSubject']);
        }
        else {
            $newCategory = null;
        }

        if (isset($body['image']) && $body['image'] != null) {
            $newImage = $body['image'];
        }
        else {
            $newImage = null;
        }

        if ($body['institution'] == "") {
            $body['institution'] = null;
        }

        if ($body['field'] == "") {
            $body['field'] = null;
        }

        if ($body['phone'] == 0) {
            $body['phone'] = null;
        }

        $newDate = new \dateTime($body['birthdate']);

        $hashedPassword = $userPasswordHasher->hashPassword(
            $newUser,
            $body['mdp']
        );

        $newUser->setEmail($body['email']);
        $newUser->setRoles(["ROLE_USER"]);        
        $newUser->setPassword($hashedPassword);
        $newUser->setFirstName($body['firstName']);
        $newUser->setLastName($body['lastName']);
        $newUser->setInstitution($body['institution']);
        $newUser->setField($body['field']);
        $newUser->setPhone($body['phone']);
        $newUser->setBirthdate($newDate);
        $newUser->setRegisteredDate(new \DateTime('now'));
        $newUser->setImage($newImage);
        $newUser->setRole($newRole);
        $newUser->setMasteredSubject($newCategory);
        $newUser->setIsVerified(True);
        $newUser->setValidAccount(True);


        $em->persist($newUser);
        $em->flush();
         
        $response = new jsonResponse($body);
        $response->setContent(json_encode('L\'utilisateur a ete ajouter'));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF-8');
 
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($newUser, 'json');
        $response = new Response($json);
        
        return $response;
    }
 
}