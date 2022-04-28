<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Security\AuthUserAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private function roleManager(ManagerRegistry $doctrine) : RoleRepository {
        return $doctrine->getManager()->getRepository(Role::class);
    }

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AuthUserAuthenticator $authenticator, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_ADMIN']);

            $role = $this->roleManager($doctrine)->find(1);
            $user->setRole($role);
            $user->setValidAccount(0);

            $today = new \DateTime('now');
            $user->setRegisteredDate($today);

            $entityManager->persist($user);
            $entityManager->flush();


            //$email = (new Email())
            //->from('aa@example.com')
            //->to('you@example.com')
            
    
            //->subject('Inscription d\un utilisateur')
            //->text('L\'uilisateur ' . $user->getFirstName() . $user->getLastName() . ' a été inscrit à la base de donnée de l\'interface web.')
            //->html('<p>Ceci est un message automatisé :</p><p>L\'uilisateur ' . $user->getFirstName() . $user->getLastName() . ' a été inscrit à la base de donnée de l\'interface web.</p><p>Merci, bonne journée</p>');

            //$mailer->send($email);

            
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre courriel a bien été confirmer.');

        return $this->redirectToRoute('app_register');
    }
}
