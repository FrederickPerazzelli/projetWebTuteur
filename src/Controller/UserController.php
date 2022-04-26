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

class UserController extends AbstractController
{
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

    #[Route('/profile/{id}', name: 'profile')]
    public function getProfile(EntityManagerInterface $em, $id)
    {
        $user = $em->getRepository(User::class)->find($id);

        if ($user->getImage())
                $user->setImage(base64_encode(stream_get_contents($user->getImage())));

        $form = $this->createForm(ProduitType::class, $produit);
        $form->add('modifier', SubmitType::class, ['label' => 'Modification du produit']);

        $form->handleRequest($request);

        if ($request->isMethod('post') && $form->isValid()) {
            $em->persist($produit);
            $em->flush();
            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Le produit #' . $id . ' a bien été modifié');

            return $this->redirect($this->generateUrl('produits'));
        }

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }

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
}
