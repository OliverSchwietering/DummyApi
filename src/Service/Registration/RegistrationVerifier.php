<?php

namespace App\Service\Registration;

use App\Entity\RegistrationToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationVerifier
{
    public function __construct(private EntityManagerInterface $entityManager){}
    public function verify(string $registrationToken): ?bool
    {
        /** @var RegistrationToken $registrationTokenEntity */
        $registrationTokenEntity = $this->entityManager->getRepository(RegistrationToken::class)->findOneBy(["token" => $registrationToken]);
        if($registrationTokenEntity === null) return null;

        $now = (new \DateTime())->getTimestamp();
        if(($now - $registrationTokenEntity->getExpiresAt()) > 3600) return false;

        $user = $registrationTokenEntity->getUser();
        $user->setIsVerified(true);
        $this->entityManager->remove($registrationTokenEntity);
        $this->entityManager->flush();

        return true;
    }
}