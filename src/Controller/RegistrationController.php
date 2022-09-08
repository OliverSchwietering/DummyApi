<?php

namespace App\Controller;

use App\Entity\RegistrationToken;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Service\Registration\RegistrationVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    public function __construct(private MailerInterface $mailer){}

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

            $entityManager->persist($user);
            $entityManager->flush();


            // generate a signed url and email it to the user
            $timestamp = (new \DateTime())->getTimestamp();

            $registrationToken = new RegistrationToken();
            $registrationToken->setToken(md5((rand(0,1000) * $timestamp)));
            $registrationToken->setUser($user);
            $registrationToken->setExpiresAt(($timestamp + 3600));

            $entityManager->persist($registrationToken);
            $entityManager->flush();

            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@progressio-development.de', 'Dummy Api'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig');

                $context = $email->getContext();
                $context['verifyUrl'] = $this->generateUrl('app_verify_email', [], UrlGeneratorInterface::ABSOLUTE_URL);
                $context['registrationToken'] = $registrationToken->getToken();

                $email->context($context);

                $this->mailer->send($email);
            }
            catch (\Throwable $e){
                $this->addFlash('danger', $e->getMessage());
            }
            /*$this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@progressio-development.de', 'Dummy Api'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_register_pending');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'hideNav' => true
        ]);
    }

    #[Route('/register/pending', name: 'app_register_pending')]
    public function pending(Request $request): Response
    {
        return $this->render('registration/waiting_for_email_confirmation.html.twig', [
            'hideNav' => true
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $registrationToken = $request->query->get('registrationToken') ?? $request->query->get('registrationtoken');
        if($registrationToken === null) return $this->redirectToRoute('app_register');
        $registrationVerifier = new RegistrationVerifier($entityManager);
        $isVerified = $registrationVerifier->verify($registrationToken);
        if($isVerified === false) {
            $this->addFlash('danger', 'Invalid registration token');
            return $this->redirectToRoute('app_register');
        }
        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }
}
