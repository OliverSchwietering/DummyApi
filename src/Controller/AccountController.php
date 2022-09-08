<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Message\Flash\FlashMessageGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/account')]
class AccountController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine){
    }

    #[Route('/profile', name: 'app_account_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('account/profile.html.twig');
    }

    #[Route('/settings', name: 'app_account_settings')]
    public function settings(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('account/settings.html.twig');
    }

    #[Route('/profile/set-password', name: 'app_account_profile_set_password')]
    public function setPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $flashMessageGenerator = new FlashMessageGenerator($request);

        if(!$request->request->has('oldPassword') || !$request->request->has('newPassword')){
            $flashMessageGenerator->generateError('Not all required parameters given');
            return $this->redirectToRoute('app_account_profile');
        }

        $user = $this->getUser();
        $passwordIsValid = $userPasswordHasher->isPasswordValid($user, $request->request->get('oldPassword'));
        if(!$passwordIsValid){
            $flashMessageGenerator->generateError('Access denied: Password is incorrect');
            return $this->redirectToRoute('app_account_profile');
        }

        $newPasswordHash = $userPasswordHasher->hashPassword($user, $request->request->get('newPassword'));
        $userEntity = $this->doctrine->getRepository(User::class)->findOneBy(["id" => $user->getId()]);
        $userEntity->setPassword($newPasswordHash);

        $this->doctrine->getManager()->flush();

        $flashMessageGenerator->generateSuccess('Password reset successful');
        return $this->redirectToRoute('app_logout');
    }

}
