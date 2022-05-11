<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // Permet de se connecter sur l'application mobile
    public function mobileLogin(Request $request, EntityManagerInterface $em): Response
    {
        $body = json_decode(
            $request->getContent(), true
        );

        $user = $em->getRepository(User::class)->findOneBy(['email' => $body['email']]);
        $response = new jsonResponse();

        if ($user) {
            if (password_verify($body['mdp'], $user->getPassword())) {
                $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
                $json = $serializer->serialize($user, 'json');
                $response = new Response($json);
            }
            else {
                $body['err'] = "Mot de passe incorrect";
                $response->setContent(json_encode($body));
            }
        }
        else {
            $body['err'] = "Il n\'y a pas d\'utilisateur pour ce courriel";
            $response->setContent(json_encode($body));
        }

        $response->setCharset('UTF-8');

        return $response;
    }
}
