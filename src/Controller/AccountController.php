<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/account')]
class AccountController extends AbstractController
{
    #[Route('/profile', name: 'app_account_profile')]
    public function profile(): Response
    {
        return $this->render('account/profile.html.twig');
    }
    #[Route('/settings', name: 'app_account_settings')]
    public function settings(): Response
    {
        return $this->render('account/settings.html.twig');
    }
}
