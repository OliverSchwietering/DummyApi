<?php

namespace App\Service\Authenticator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class VerifyAuthenticator extends \Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator
{
    public function __construct(private EntityManagerInterface $entityManager, private UrlGeneratorInterface $urlGenerator){}

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $request->request->get('_username') !== null;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["email" => $request->request->get('_username')]);
        if($user === null) throw new CustomUserMessageAuthenticationException('User does not exist');
        if($user->isVerified() === false) throw new CustomUserMessageAuthenticationException('User is not verified');
        return new Passport(new UserBadge($user->getUserIdentifier()), new PasswordCredentials($request->request->get('_password')));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_admin'));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->getFlashBag()->add('danger', $exception->getMessage());
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
        //return new JsonResponse(["message" => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}